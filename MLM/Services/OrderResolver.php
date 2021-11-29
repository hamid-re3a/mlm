<?php

namespace MLM\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MLM\Interfaces\Commission;
use MLM\Jobs\UpdateUserRanksJob;
use MLM\Models\OrderedPackage;
use MLM\Services\Plans\RegisterOder;
use MLM\Services\Plans\SpecialOrder;
use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;
use Packages\Services\Grpc\Acknowledge;
use Packages\Services\Grpc\Package;
use Packages\Services\Grpc\PackageCheck;
use Packages\Services\Grpc\PackageClientFacade;
use User\Models\User;
use User\Services\UserService;

class OrderResolver
{
    public $order;
    /**
     * @var RegisterOder
     */
    private $plan;
    /**
     * @var $user_service UserService
     */
    private $user_service;

    /**
     * @var $user User
     */
    private $user;

    public function __construct(Order &$order)
    {
        $this->order = $order;
        $this->planDefiner();
        $this->user_service = app(UserService::class);
        try {
            $this->user = $this->user_service->findByIdOrFail($this->order->getUserId());
        } catch (\Exception $e) {
            return [false, trans('order.responses.user-not-valid')];
        }
    }

    private function planDefiner()
    {
        switch ($this->order->getPlan()) {
            case OrderPlans::ORDER_PLAN_SPECIAL:
            case OrderPlans::ORDER_PLAN_COMPANY:
                $this->plan = app(SpecialOrder::class);
                break;
            case OrderPlans::ORDER_PLAN_START:
            case OrderPlans::ORDER_PLAN_PURCHASE:
            default:
                $this->plan = app(RegisterOder::class);
                break;
        }

    }


    public function handle(): array
    {

        $processed = [true, 'handle'];
        if ($this->order->getIsCommissionResolvedAt())
            return $processed;
        if ($this->order->getIsPaidAt()) {

            list($bool, $msg, $problem_level) = $this->resolve();
//            if($this->plan->getName() == PLAN_BINARY)
//            dd([$bool, $msg, $problem_level]);
            return [$bool, $msg];
        } else {
            $processed = [false, 'handle.notPaid'];
        }
        return $processed;
    }

    /**
     * @return array [bool,string,int]
     */
    public function resolve(): array
    {
        DB::beginTransaction();
        $problem_level = 0;

        try {
            list($bool, $msg) = $this->isValid();
            if ($bool) {

                list($bool, $msg) = $this->addUserToNetwork();
                if ($bool) {
                    list($bool, $msg) = $this->resolveCommission();
                    if ($bool) {
                        $ordered_package = OrderedPackage::query()->where('order_id', $this->order->getId())
                            ->first();
                        $ordered_package->is_commission_resolved_at = Carbon::make($this->order->getIsCommissionResolvedAt());
                        $ordered_package->save();

                        UpdateUserRanksJob::dispatch(User::query()->find($this->order->getUserId()));

                        DB::commit();
                        return [true, 'resolve', $problem_level];
                    } else {
                        $problem_level = 3;
                    }
                } else {
                    $problem_level = 2;
                }
            } else {
                $problem_level = 1;
            }
        } catch (\Exception $exception) {
            Log::error('OrderResolver@resolve =>' . $exception->getMessage());
        }


        DB::rollBack();
        return [false, $msg ?? 'resolve', $problem_level];
    }


    /**
     * @return array [bool,string]
     */
    public function simulateValidation(): array
    {
        DB::beginTransaction();
        list($bool, $msg) = $this->isValid();
        if ($bool) {
            list($bool, $msg) = $this->addUserToNetwork(true);
            if ($bool) {
                DB::rollBack();
                return [true, 'resolve'];
            }

        }

        DB::rollBack();
        return [$bool, $msg ?? 'resolve'];

    }

    /**
     * @return array [bool,string]
     */
    public function resolveCommission(): array
    {
        if (!$this->order->getIsCommissionResolvedAt()) {
            $isItOk = true;
            try {
                DB::beginTransaction();
                /** @var  $commission Commission */
                foreach ($this->plan->getCommissions() as $commission)
                    $isItOk = $isItOk && $commission->calculate($this->order);
                if (!$isItOk) {
                    DB::rollBack();
                    return [false, trans('responses.resolveCommission')];
                }

                DB::commit();
                $this->order->setIsCommissionResolvedAt(now()->toDateTimeString());
            } catch (\Throwable $e) {
                DB::rollBack();
                return [false, trans('responses.resolveCommission')];
            }

        }
        return [true, trans('responses.resolveCommission')];
    }

    /**
     * @return array [bool,string]
     */
    public function isValid(): array
    {
        try {
            $ordered_package = OrderedPackage::query()->where('order_id', $this->order->getId())->first();
            switch ($this->order->getPlan()) {
                case OrderPlans::ORDER_PLAN_START:
                case OrderPlans::ORDER_PLAN_START_50:
                case OrderPlans::ORDER_PLAN_START_75:
                    if ($this->user->hasAnyValidOrder())
                        return [false, trans('order.responses.you-should-order-other-plan-you-have-already-start-plan')];
                    break;
                case OrderPlans::ORDER_PLAN_PURCHASE:
                    if (!$this->user->hasAnyValidOrder())
                        return [false, trans('order.responses.you-should-order-starter-plan-first')];
                    break;
                case OrderPlans::ORDER_PLAN_SPECIAL:
                    if ($ordered_package->package->short_name != 'A1')
                        return [false, trans('order.responses.you-have-to-select-a1-for-special-package')];
                    break;
                case OrderPlans::ORDER_PLAN_COMPANY:
                    if ($ordered_package->package->short_name != 'P4')
                        return [false, trans('order.responses.you-have-to-select-p4-for-company-package')];
                    break;
                default:
                    return [false, trans('responses.not-valid-plan')];
            }
            if($this->user->hasAnyValidOrder()){
                $packages = new PackageCheck;
                $packages->setPackageIndexId($this->user->biggestOrderedPackage()->package_id);
                $packages->setPackageToBuyId($ordered_package->package_id);
                /** @var $ack Acknowledge */
                $ack = PackageClientFacade::packageIsInBiggestPackageCategory($packages);
                if(!$ack->getStatus())
                    return [false, trans('order.responses.selected-package-should-be-greater-or-equal-to-previous-package')];

            }

            if(OrderedPackage::query()->where('user_id', $this->user->id)->active()->count() > 9)
                return [false, trans('order.responses.only-10-active-package-is-allowed')];
        } catch (\Exception $exception) {
            Log::error('OrderResolver@isValid =>' . $exception->getMessage());
            return [false, trans('responses.unknown')];
        }
        // check user if he is in tree
        return [true, trans('responses.isValid')];
    }


    /**
     * @param bool $simulate
     * @return array [bool,string]
     */

    private function addUserToNetwork($simulate = false): array
    {
        try {
            if (in_array($this->order->getPlan(), [OrderPlans::ORDER_PLAN_START, OrderPlans::ORDER_PLAN_COMPANY, OrderPlans::ORDER_PLAN_SPECIAL]))
                return (new AssignNodeResolver($this->order))->handle($simulate);
        } catch (\Exception $exception) {
            Log::error('OrderResolver@addUserToNetwork =>' . $exception->getMessage());
            return [false, trans('responses.unknown')];
        }
        return [true, trans('responses.isValid')];
    }

}

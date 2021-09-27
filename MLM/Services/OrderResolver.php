<?php

namespace MLM\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use MLM\Interfaces\Commission;
use MLM\Jobs\UpdateUserRanksJob;
use MLM\Models\OrderedPackage;
use MLM\Services\Plans\RegisterOder;
use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;
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
        $this->plan = app(RegisterOder::class);
        $this->user_service = app(UserService::class);
        try {
            $this->user = $this->user_service->findByIdOrFail($this->order->getUserId());
        } catch (\Exception $e) {
            return [false, trans('order.responses.user-not-valid')];
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
//        if ($bool) {
//            list($bool, $msg) = $this->addUserToNetwork(true);
//            if ($bool) {
//                DB::rollBack();
//                return [true, 'resolve'];
//            }
//
//        }

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
        if ($this->order->getPlan() != OrderPlans::ORDER_PLAN_START) {

            if (!$this->user->hasAnyValidOrder())
                return [false, trans('order.responses.you-should-order-starter-plan-first')];
        } else {
            if ($this->user->hasAnyValidOrder())
                return [false, trans('order.responses.you-should-order-starter-plan-first')];
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
        if ($this->order->getPlan() == OrderPlans::ORDER_PLAN_START)
            return (new AssignNodeResolver($this->order))->handle($simulate);
        return [true, trans('responses.isValid')];
    }

}

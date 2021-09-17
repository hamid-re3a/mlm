<?php

namespace MLM\Services;


use Illuminate\Support\Facades\DB;
use MLM\Interfaces\Commission;
use MLM\Models\OrderedPackage;
use MLM\Services\Plans\RegisterOder;
use Orders\Services\Grpc\Order;
use Orders\Services\Grpc\OrderPlans;

class OrderResolver
{
    public $order;
    /**
     * @var RegisterOder
     */
    private $plan;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->plan = app(RegisterOder::class);
    }


    public function handle(): array
    {

        $processed = [true, 'handle'];
        if ($this->order->getIsResolvedAt())
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
                        ->where('package_id', $this->order->getPackageId())->first();
                    $ordered_package->is_commission_resolved_at = $this->order->getIsCommissionResolvedAt();
                    $ordered_package->save();

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
     * @throws \Throwable
     */
    public function simulateValidation(): array
    {
        DB::beginTransaction();

        list($bool, $msg) = $this->isValid();
        if ($bool) {
            list($bool, $msg) = $this->addUserToNetwork(true);
            if ($bool) {
                DB::rollBack(0);
                return [true, 'resolve'];
            }

        }

        DB::rollBack(0);
        return [false, $msg ?? 'resolve'];

    }

    /**
     * @return array [bool,string]
     */
    public function resolveCommission(): array
    {
        if (!$this->order->getIsCommissionResolvedAt()) {
            $isItOk = true;
//            try {
                DB::beginTransaction();
                /** @var  $commission Commission */
                foreach ($this->plan->getCommissions() as $commission)
                    $isItOk = $isItOk && $commission->calculate($this->order);
                if (!$isItOk) {
                    DB::rollBack();
                    return [false, trans('responses.resolveCommission')];
                }

                DB::commit();
                $this->order->setIsCommissionResolvedAt(now()->toString());
//            } catch (\Throwable $e) {
//                DB::rollBack();
//                return [false, trans('responses.resolveCommission')];
//            }

        }
        return [true, trans('responses.resolveCommission')];
    }

    /**
     * @return array [bool,string]
     */
    public function isValid(): array
    {

//        if ($this->order->getPlan() != OrderPlans::ORDER_PLAN_START) {
//            $check_start_plan = request()->user->paidOrders()->where('plan', '=', ORDER_PLAN_START)->count();
//            if (!$check_start_plan)
//                return [false, trans('order.responses.you-should-order-starter-plan-first')];
//        }
        // check user if he is in tree
        return [true, trans('responses.isValid')];
    }


    /**
     * @param bool $simulate
     * @return array [bool,string]
     */

    private function addUserToNetwork($simulate = false): array
    {
        return (new AssignNodeResolver($this->order))->handle($simulate);
    }

}

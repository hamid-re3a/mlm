<?php


namespace MLM\Services\Commissions;


use MLM\Interfaces\Commission;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\OrderedPackage;
use Orders\Services\Grpc\Order;
use User\Models\User;
use User\Services\UserService;

class IndirectSellCommission implements Commission
{

    public function calculate(Order $order): bool
    {
        $user = app(UserService::class)->findByIdOrFail($order->getUserId());
        $package = OrderedPackage::query()->where('order_id', $order->getId())->firstOrFail();

        if (!CommissionModel::query()->where('ordered_package_id', $package->id)->type($this->getType())->exists()) {

            $this->indirectCommissionToFather($user, $package);
        }

        return true;
    }

    public function backupCommissionPlan(): ?Commission
    {
        return null;
    }

    public function getType(): string
    {
        return INDIRECT_SELL_COMMISSION;
    }

    /**
     * @param $user
     * @param $package
     * @param $level
     */
    private function indirectCommissionToFather(User $user, OrderedPackage $package, $level = 0): void
    {

    }

}

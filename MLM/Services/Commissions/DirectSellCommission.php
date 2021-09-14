<?php


namespace MLM\Services\Commissions;

use MLM\Interfaces\Commission;
use MLM\Jobs\DirectSellCommissionJob;
use MLM\Models\OrderedPackage;
use Orders\Services\Grpc\Order;
use User\Models\User;

class DirectSellCommission implements Commission
{

    public function calculate(Order $order): bool
    {
        $user = User::query()->findOrFail($order->getUserId());
        $package = OrderedPackage::query()->where('order_id', $order->getId())->firstOrFail();
        DirectSellCommissionJob::dispatch($user,$package);
        return true;
    }

    public function backupCommissionPlan(): ?Commission
    {
        return null;
    }

    public function getType(): string
    {
        return DIRECT_SELL_COMMISSION;
    }

}

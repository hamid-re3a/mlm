<?php


namespace MLM\Services\Commissions;

use MLM\Interfaces\Commission;
use Orders\Services\Order;
use User\Models\User;

class DirectSellCommission implements Commission
{

    public function calculate(Order $order): bool
    {
        $user = User::query()->findOrFail($order->getUserId());
//        $user

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

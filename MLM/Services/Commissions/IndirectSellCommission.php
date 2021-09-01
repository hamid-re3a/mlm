<?php


namespace MLM\Services\Commissions;


use MLM\Interfaces\Commission;
use Orders\Services\Order;

class IndirectSellCommission implements Commission
{

    public function calculate(Order $order): bool
    {
        // TODO: Implement calculate() method.
    }

    public function backupCommissionPlan(): ?Commission
    {
        return null;
    }

    public function getType(): string
    {
        return INDIRECT_SELL_COMMISSION;
    }

}

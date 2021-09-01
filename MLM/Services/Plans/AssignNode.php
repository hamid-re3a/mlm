<?php
namespace MLM\Services\Plans;

use MLM\Interfaces\Plan;
use Illuminate\Support\Collection;
use MLM\Services\Commissions\DirectSellCommission;
use MLM\Services\Commissions\IndirectSellCommission;

class AssignNode implements Plan
{

    public function getCommissions(): ?Collection
    {
        return Collection::make([app(DirectSellCommission::class),app(IndirectSellCommission::class)]);
    }
    public function getName(): string
    {
        return 'assign-node';
    }

    public function backupPlan(): ?Plan
    {
        return null;
    }
}

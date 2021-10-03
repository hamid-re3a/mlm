<?php
namespace MLM\Services\Plans;

use Illuminate\Support\Collection;
use MLM\Interfaces\Plan;
use MLM\Services\Commissions\BinaryCommission;
use MLM\Services\Commissions\DirectSellCommission;
use MLM\Services\Commissions\IndirectSellCommission;
use MLM\Services\Commissions\TrainerBonusCommission;

class SpecialOrder implements Plan
{

    public function getCommissions(): ?Collection
    {
        return Collection::make([
        ]);
    }
    public function getName(): string
    {
        return 'special-order';
    }

    public function backupPlan(): ?Plan
    {
        return null;
    }
}

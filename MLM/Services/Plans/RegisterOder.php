<?php
namespace MLM\Services\Plans;

use Illuminate\Support\Collection;
use MLM\Interfaces\Plan;
use MLM\Services\Commissions\BinaryCommission;
use MLM\Services\Commissions\DirectSellCommission;
use MLM\Services\Commissions\IndirectSellCommission;
use MLM\Services\Commissions\QuickStartBonusCommission;
use MLM\Services\Commissions\ResidualBonusCommission;
use MLM\Services\Commissions\TrainerBonusCommission;

class RegisterOder implements Plan
{

    public function getCommissions(): ?Collection
    {
        return Collection::make([
            app(BinaryCommission::class),

            app(DirectSellCommission::class),
            app(IndirectSellCommission::class),

            app(QuickStartBonusCommission::class),
            app(TrainerBonusCommission::class),

            app(ResidualBonusCommission::class),
        ]);
    }
    public function getName(): string
    {
        return 'register-order';
    }

    public function backupPlan(): ?Plan
    {
        return null;
    }
}

<?php


namespace MLM\Services\Commissions;


use MLM\Interfaces\Commission;
use MLM\Jobs\TrainerBonusCommissionJob;
use MLM\Models\OrderedPackage;
use Orders\Services\Grpc\Order;
use User\Models\User;

class TrainerBonusCommission implements Commission
{

    public function calculate(Order $order): bool
    {

        $user = User::query()->findOrFail($order->getUserId());
        $package = OrderedPackage::query()->where('order_id', $order->getId())->firstOrFail();
        TrainerBonusCommissionJob::dispatch($user,$package);
        return true;


    }

    public function backupCommissionPlan(): ?Commission
    {
        return null;
    }

    public function getType(): string
    {
        return TRAINER_BONUS_COMMISSION;
    }

}

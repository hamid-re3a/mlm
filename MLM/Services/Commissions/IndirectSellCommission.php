<?php


namespace MLM\Services\Commissions;


use MLM\Interfaces\Commission;
use MLM\Jobs\IndirectSellCommissionJob;
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
        IndirectSellCommissionJob::dispatch($user,$package);
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



}

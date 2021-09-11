<?php


namespace MLM\Services\Commissions;

use App\Jobs\Wallet\WalletDepositJob;
use MLM\Interfaces\Commission;
use MLM\Jobs\BinaryCommissionJob;
use MLM\Jobs\DirectSellCommissionJob;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\OrderedPackage;
use MLM\Models\OrderedPackagesIndirectCommission;
use MLM\Models\Rank;
use Orders\Services\Order;
use User\Models\User;
use Wallets\Services\Deposit;

class BinaryCommission implements Commission
{

    public function calculate(Order $order): bool
    {
        $user = User::query()->findOrFail($order->getUserId());
        $package = OrderedPackage::query()->where('order_id', $order->getId())->firstOrFail();

        BinaryCommissionJob::dispatch($user,$package);
        return true;
    }

    public function backupCommissionPlan(): ?Commission
    {
        return null;
    }

    public function getType(): string
    {
        return BINARY_COMMISSION;
    }


}

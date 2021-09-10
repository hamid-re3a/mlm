<?php


namespace MLM\Services\Commissions;


use App\Jobs\Wallet\WalletDepositJob;
use MLM\Interfaces\Commission;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\OrderedPackage;
use MLM\Models\OrderedPackagesIndirectCommission;
use Orders\Services\Order;
use User\Models\User;
use Wallets\Services\Deposit;

class IndirectSellCommission implements Commission
{

    public function calculate(Order $order): bool
    {
        $user = User::query()->findOrFail($order->getUserId());
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

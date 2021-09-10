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

        if (is_null($user->referralTree->parent) || is_null($user->referralTree->parent->user))
            return;
        $biggest_active_package = $user->referralTree->parent->user->biggestActivePackage();
        if ($biggest_active_package) {
            /** @var  $indirect_found OrderedPackagesIndirectCommission */
            $indirect_found = $biggest_active_package->indirectCommission()->where('level', $level)->first();
            if ($indirect_found) {
                $commission_amount = ($package->price * $indirect_found->percentage / 100);

                /** @var $depositService  Deposit*/
                $depositService = app(Deposit::class);
                $depositService->setUserId($user->referralTree->parent->user->id);
                $depositService->setAmount($commission_amount);
                $depositService->setWalletName('Earning Wallet');

                $depositService->setDescription(serialize([
                    'description' => 'Commission # ' . $this->getType()
                ]));
                $depositService->setType('Commission');
                $depositService->setSubType('Indirect Sell');
                $depositService->setServiceName('mlm');


                $commission = $user->referralTree->parent->user->commissions()->create([
                    'amount' => $commission_amount,
                    'ordered_package_id' => $package->id,
                    'type' => $this->getType(),
                ]);
                if ($commission) {
                    $depositService->setPayloadId($commission->id);
                    WalletDepositJob::dispatch($depositService)->onConnection('rabbit')->onQueue('subscriptions');
                }
            }
        }

        if ($level == 7)
            return;
        $this->indirectCommissionToFather($user->referralTree->parent->user, $package, ++$level);

    }

}

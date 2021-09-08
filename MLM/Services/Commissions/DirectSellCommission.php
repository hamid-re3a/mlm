<?php


namespace MLM\Services\Commissions;

use App\Jobs\Wallet\WalletDepositJob;
use MLM\Interfaces\Commission;
use MLM\Models\Package;
use MLM\Models\Commission as CommissionModel;
use Orders\Services\Order;
use User\Models\User;
use Wallets\Services\Deposit;

class DirectSellCommission implements Commission
{

    public function calculate(Order $order): bool
    {
        $user = User::query()->findOrFail($order->getUserId());
        $package = Package::query()->where('order_id', $order->getId())->firstOrFail();

        if (!CommissionModel::query()->where('package_id', $package->id)->type($this->getType())->exists()) {
            /** @var  $biggest_active_package Package */
            $biggest_active_package = $user->referralTree->parent->user->biggestActivePackage();
            if ($biggest_active_package) {

                $is_eligible_for_quick_start_bonus = false;
                if ($user->referralTree->parent->user->eligibleForQuickStartBonus()){
                    $is_eligible_for_quick_start_bonus = true;
                }

                $percentage = ($is_eligible_for_quick_start_bonus) ? 12 : $biggest_active_package->direct_percentage;
                $commission_amount = ($package->price * $percentage / 100);


                /** @var $depositService  Deposit*/
                $depositService = app(Deposit::class);
                $depositService->setUserId($user->referralTree->parent->user->id);
                $depositService->setAmount($commission_amount);
                $depositService->setWalletName('Earning Wallet');

                $depositService->setDescription(serialize([
                    'description' => 'Commission # ' . $this->getType() . $is_eligible_for_quick_start_bonus ? ' - Quick Start bonus ' : ''
                ]));
                $depositService->setType('Commission');
                $depositService->setSubType('Direct Sell');
                $depositService->setServiceName('mlm');

                /** @var $commission CommissionModel */
                $commission = $user->referralTree->parent->user->commissions()->create([
                    'amount' => $commission_amount,
                    'package_id' => $package->id,
                    'type' => $this->getType(),
                ]);

                if ($commission) {
                    $depositService->setPayloadId($commission->id);
                    WalletDepositJob::dispatch($depositService)->onConnection('rabbit')->onQueue('subscriptions');
                }
            }
        }
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

<?php


namespace MLM\Services\Commissions;

use App\Jobs\Wallet\WalletDepositJob;
use MLM\Interfaces\Commission;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\Package;
use MLM\Models\PackagesIndirectCommission;
use MLM\Models\Rank;
use Orders\Services\Order;
use User\Models\User;
use Wallets\Services\Deposit;

class BinaryCommission implements Commission
{

    public function calculate(Order $order): bool
    {
        $user = User::query()->findOrFail($order->getUserId());

        $package = Package::query()->where('order_id', $order->getId())->firstOrFail();

        $this->binaryCommissionToFather($user, $package);
    }

    public function backupCommissionPlan(): ?Commission
    {
        return null;
    }

    public function getType(): string
    {
        return BINARY_COMMISSION;
    }

    private function binaryCommissionToFather(User $user, Package $package)
    {

        if (!is_null($user->referralTree->parent) && !is_null($user->referralTree->parent->user)) {
            $parent = $user->referralTree->parent->user;
            if ($parent->hasCompletedBinaryLegs()) {

                $biggest_active_package = $parent->biggestActivePackage();
                if ($biggest_active_package) {
                    $left_binary_children_price = $parent->binaryTree->leftSideChildrenPackagePrice();
                    $right_binary_children_price = $parent->binaryTree->rightSideChildrenPackagePrice();
                    $weaker_leg_price = $left_binary_children_price >= $right_binary_children_price ? $right_binary_children_price : $left_binary_children_price;

                    $first_convert_amount = $weaker_leg_price - $parent->binaryTree->converted_points;
                    if ($first_convert_amount > 0) {

                        /** @var $rank Rank */
                        $rank = getAndUpdateUserRank($parent);

                        $amount = $parent->commissions()->where('type', $this->getType())
                            ->whereDate('created_at', now()->toDate())->sum('amount');
                        if ($first_convert_amount + $amount > $rank->cap) {

                            $convert_amount = $rank->cap - $amount;
                            $convert_amount = ($convert_amount < 0) ? 0 : $convert_amount;


                            $cap_amount = $first_convert_amount - $convert_amount;

                            $parent->commissions()->create([
                                'amount' => $cap_amount,
                                'package_id' => $package->id,
                                'type' => 'Cap',
                            ]);
                        } else {
                            $convert_amount = $first_convert_amount;
                        }

                        if ($convert_amount > 0) {
                            $commission_amount = $convert_amount * $biggest_active_package->binary_percentage;

                            /** @var $depositService  Deposit */
                            $depositService = app(Deposit::class);
                            $depositService->setUserId($user->referralTree->parent->user->id);
                            $depositService->setAmount($commission_amount);
                            $depositService->setWalletName('Earning Wallet');

                            $depositService->setDescription(serialize([
                                'description' => 'Commission # ' . $this->getType()
                            ]));
                            $depositService->setType('Commission');
                            $depositService->setSubType('Binary');
                            $depositService->setServiceName('mlm');


                            $commission = $parent->commissions()->create([
                                'amount' => $commission_amount,
                                'package_id' => $package->id,
                                'type' => $this->getType(),
                            ]);
                            if ($commission) {
                                $depositService->setPayloadId($commission->id);
                                WalletDepositJob::dispatch($depositService)->onConnection('rabbit')->onQueue('subscriptions');

                            }

                        }

                        $parent->binaryTree->converted_points = $weaker_leg_price;
                        $parent->binaryTree->save();
                    }
                }
            }
            return $this->binaryCommissionToFather($parent, $package);
        }
        return null;


    }

}

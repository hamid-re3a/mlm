<?php

namespace MLM\Jobs;

use App\Jobs\Wallet\WalletDepositJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\OrderedPackage;
use MLM\Models\Rank;
use User\Models\User;
use Wallets\Services\Deposit;

class BinaryCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $package;
    private $user;


    public function __construct(User $user, OrderedPackage $package)
    {
        $this->package = $package;
        $this->user = $user;
    }

    public function handle()
    {

        if (!is_null($this->user->binaryTree->parent) && !is_null($this->user->binaryTree->parent->user)) {
            $parent = $this->user->binaryTree->parent->user;
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
                                'ordered_package_id' => $this->package->id,
                                'type' => 'Cap',
                            ]);
                        } else {
                            $convert_amount = $first_convert_amount;
                        }

                        if ($convert_amount > 0) {
                            $commission_amount = $convert_amount * $biggest_active_package->binary_percentage;

                            /** @var $depositService  Deposit */
                            $depositService = app(Deposit::class);
                            $depositService->setUserId($this->user->referralTree->parent->user->id);
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
                                'ordered_package_id' => $this->package->id,
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
            BinaryCOmmissionJob::dispatch($parent, $this->package);
        }
    }

    public function getType(): string
    {
        return BINARY_COMMISSION;
    }

}

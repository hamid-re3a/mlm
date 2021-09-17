<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\OrderedPackage;
use MLM\Models\Rank;
use User\Models\User;
use Wallets\Services\Grpc\Deposit;

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

                            /** @var $deposit_service_object  Deposit */
                            $deposit_service_object = app(Deposit::class);
                            $deposit_service_object->setUserId($this->user->referralTree->parent->user->id);
                            $deposit_service_object->setAmount($commission_amount);
                            $deposit_service_object->setWalletName(\Wallets\Services\Grpc\WalletNames::EARNING);

                            $deposit_service_object->setDescription(serialize([
                                'description' => 'Commission # ' . $this->getType()
                            ]));
                            $deposit_service_object->setType('Commission');
                            $deposit_service_object->setSubType('Binary');
                            $deposit_service_object->setServiceName('mlm');

                            payCommission($deposit_service_object,$parent,$this->package,$this->getType());

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

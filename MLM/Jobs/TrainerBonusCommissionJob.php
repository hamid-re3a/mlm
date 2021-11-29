<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\OrderedPackage;
use MLM\Services\CommissionResolver;
use User\Models\User;
use User\Services\UserService;
use Wallets\Services\Grpc\Deposit;

class TrainerBonusCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private $package;
    private $user;


    public function __construct(User $user, OrderedPackage $package)
    {
        $this->queue = env('QUEUE_COMMISSIONS_NAME','mlm_commissions');
        $this->package = $package;
        $this->user = $user;
    }

    public function handle(UserService $user_service)
    {
        if(!getSetting('TRAINER_BONUS_COMMISSION_IS_ACTIVE')){
            return ;
        }
        if (!is_null($this->user->referralTree->parent) && !is_null($this->user->referralTree->parent->user_id)) {
            $parent = $user_service->findByIdOrFail($this->user->referralTree->parent->user_id);

            if (arrayHasValue(TRADING_PROFIT_COMMISSION, $parent->deactivated_commission_types)) {
                return;
            }
            if (!is_null($parent->referralTree->parent) && !is_null($parent->referralTree->parent->user_id)) {
                $grand_parent = $user_service->findByIdOrFail($parent->referralTree->parent->user_id);

                if (!CommissionModel::query()->where('user_id', $grand_parent->id)->type($this->getType())->exists())
                    if ($grand_parent->eligibleForQuickStartBonus()) {
                        /** @var  $biggest_active_package OrderedPackage */
                        $biggest_active_package = $grand_parent->biggestActivePackage();

                        if ($biggest_active_package) {
                            $left_binary_children = $grand_parent->binaryTree->leftSideChildrenIds();
                            $right_binary_children = $grand_parent->binaryTree->rightSideChildrenIds();

                            $referral_children = $grand_parent->referralTree->childrenUserIds();

                            $left_binary_sponsored_children = array_intersect($left_binary_children, $referral_children);
                            $right_binary_sponsored_children = array_intersect($right_binary_children, $referral_children);

                            if ($this->hasAtLeastOnEligibleForQuickStartUser($left_binary_sponsored_children) &&
                                $this->hasAtLeastOnEligibleForQuickStartUser($right_binary_sponsored_children)) {
                                $commission_amount = 200;
                                /** @var $deposit_service_object  Deposit */
                                $deposit_service_object = app(Deposit::class);
                                $deposit_service_object->setUserId($grand_parent->id);
                                $deposit_service_object->setAmount($commission_amount);
                                $deposit_service_object->setWalletName(\Wallets\Services\Grpc\WalletNames::EARNING);

                                $deposit_service_object->setDescription(serialize([
                                    'description' => 'Commission # ' . $this->getType()
                                ]));
                                $deposit_service_object->setType('Commission');
                                $deposit_service_object->setSubType('Trainer Bonus');

                                (new CommissionResolver)->payCommission($deposit_service_object, $grand_parent, $this->getType(),$biggest_active_package->id, $this->package->id);


                            }
                        }
                    }

            }

        }
    }

    public function getType(): string
    {
        return TRAINER_BONUS_COMMISSION;
    }

    private function hasAtLeastOnEligibleForQuickStartUser(array $left_binary_sponsored_children): bool
    {
        foreach ($left_binary_sponsored_children as $child) {
            if (User::query()->find($child)->eligibleForQuickStartBonus())
                return true;
        }
        return false;
    }
}

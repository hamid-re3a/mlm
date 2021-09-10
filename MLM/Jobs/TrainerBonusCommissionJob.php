<?php

namespace MLM\Jobs;

use App\Jobs\Wallet\WalletDepositJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\OrderedPackage;
use MLM\Services\PackageService;
use User\Models\User;
use Wallets\Services\Deposit;

class TrainerBonusCommissionJob implements ShouldQueue
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
        if (!is_null($this->user->referralTree->parent) && !is_null($this->user->referralTree->parent->user)) {
            $parent = $this->user->referralTree->parent->user;
            if (!CommissionModel::query()->where('user_id', $parent->id)->type($this->getType())->exists())
                if ($parent->eligibleForQuickStartBonus()) {

                    $left_binary_children = $parent->binaryTree->leftSideChildrenIds();
                    $right_binary_children = $parent->binaryTree->rightSideChildrenIds();

                    $referral_children = $parent->referralTree->childrenIds();

                    $left_binary_sponsored_children = array_intersect($left_binary_children, $referral_children);
                    $right_binary_sponsored_children = array_intersect($right_binary_children, $referral_children);

                    if ($this->hasAtLeastOnEligibleForQuickStartUser($left_binary_sponsored_children) &&
                        $this->hasAtLeastOnEligibleForQuickStartUser($right_binary_sponsored_children)) {
                        $commission_amount = 200;
                        /** @var $depositService  Deposit */
                        $depositService = app(Deposit::class);
                        $depositService->setUserId($parent->id);
                        $depositService->setAmount($commission_amount);
                        $depositService->setWalletName('Earning Wallet');

                        $depositService->setDescription(serialize([
                            'description' => 'Commission # ' . $this->getType()
                        ]));
                        $depositService->setType('Commission');
                        $depositService->setSubType('Trainer Bonus');
                        $depositService->setServiceName('mlm');


                        $commission = $this->user->referralTree->parent->user->commissions()->create([
                            'amount' => $commission_amount,
                            'ordered_package_id' => $this->package->id,
                            'type' => $this->getType(),
                        ]);
                        if ($commission) {
                            $depositService->setPayloadId($commission->id);
                            WalletDepositJob::dispatch($depositService)->onConnection('rabbit')->onQueue('subscriptions');
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

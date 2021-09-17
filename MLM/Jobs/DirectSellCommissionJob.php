<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\OrderedPackage;
use User\Models\User;
use Wallets\Services\Grpc\Deposit;

class DirectSellCommissionJob implements ShouldQueue
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
        if (!CommissionModel::query()->where('ordered_package_id', $this->package->id)->type($this->getType())->exists()) {
            /** @var  $biggest_active_package OrderedPackage */
            $biggest_active_package = $this->user->referralTree->parent->user->biggestActivePackage();
            if ($biggest_active_package) {
                $is_eligible_for_quick_start_bonus = false;
                if ($this->user->referralTree->parent->user->eligibleForQuickStartBonus()) {
                    $is_eligible_for_quick_start_bonus = true;
                }

                $percentage = ($is_eligible_for_quick_start_bonus) ? 12 : $biggest_active_package->direct_percentage;
                $commission_amount = ($this->package->price * $percentage / 100);


                /** @var $deposit_service_object  Deposit */
                $deposit_service_object = app(Deposit::class);
                $deposit_service_object->setUserId($this->user->referralTree->parent->user->id);
                $deposit_service_object->setAmount($commission_amount);
                $deposit_service_object->setWalletName(\Wallets\Services\Grpc\WalletNames::EARNING);

                $deposit_service_object->setDescription(serialize([
                    'description' => 'Commission # ' . $this->getType() . $is_eligible_for_quick_start_bonus ? ' - Quick Start bonus ' : ''
                ]));
                $deposit_service_object->setType('Commission');
                $deposit_service_object->setSubType('Direct Sell');


                payCommission($deposit_service_object,$this->user->referralTree->parent->user,$this->getType(),$this->package->id);

            }
        }
    }

    public function getType(): string
    {
        return DIRECT_SELL_COMMISSION;
    }
}

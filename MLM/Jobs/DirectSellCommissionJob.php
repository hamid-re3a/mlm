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

class DirectSellCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $package;
    private $user;


    public function __construct(User $user, OrderedPackage $package)
    {
        $this->queue = env('QUEUE_COMMISSIONS_NAME', 'mlm_commissions');
        $this->package = $package;
        $this->user = $user;
    }

    public function handle(UserService $user_service)
    {
        if (!getSetting('DIRECT_SELL_COMMISSION_IS_ACTIVE')) {
            return;
        }

        if (!CommissionModel::query()->where('ordered_package_id', $this->package->id)->type($this->getType())->exists()) {

            $parent = $user_service->findByIdOrFail($this->user->referralTree->parent->user_id);

            if (arrayHasValue(DIRECT_SELL_COMMISSION, $parent->deactivated_commission_types)) {
                return;
            }
            /** @var  $biggest_active_package OrderedPackage */
            $biggest_active_package = $parent->biggestActivePackage();
            if ($biggest_active_package) {
                $is_eligible_for_quick_start_bonus = false;
                if ($parent->eligibleForQuickStartBonus()) {
                    $is_eligible_for_quick_start_bonus = true;
                }

                $percentage = ($is_eligible_for_quick_start_bonus) ? 12 : $biggest_active_package->direct_percentage;
                $commission_amount = ($this->package->price * $percentage / 100);


                /** @var $deposit_service_object  Deposit */
                $deposit_service_object = app(Deposit::class);
                $deposit_service_object->setUserId($parent->id);
                $deposit_service_object->setAmount($commission_amount);
                $deposit_service_object->setWalletName(\Wallets\Services\Grpc\WalletNames::EARNING);


                $description = $this->getType();
                if ($is_eligible_for_quick_start_bonus)
                    $description .= ' - Quick Start bonus ';

                $deposit_service_object->setDescription(serialize([
                    'description' => 'Commission # ' . $description,
                    'from_user_id' => $this->package->user->id,
                    'from_user_name' => $this->package->user->full_name,
                    'from_package_name' => $this->package->package->name,
                    'from_order_id' => $this->package->order_id,
                    'for_package_name'=>$biggest_active_package->package->name,
                    'for_order_id'=>$biggest_active_package->order_id,
                ]));
                $deposit_service_object->setType('Commission');
                $deposit_service_object->setSubType('Direct Sale');


                (new CommissionResolver)->payCommission($deposit_service_object, $parent, $this->getType(), $biggest_active_package->id, $this->package->id);

            }
        }
    }

    public function getType(): string
    {
        return DIRECT_SELL_COMMISSION;
    }
}

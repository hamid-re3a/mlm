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
use Orders\Services\Order;
use User\Models\User;
use Wallets\Services\Deposit;

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


                /** @var $depositService  Deposit */
                $depositService = app(Deposit::class);
                $depositService->setUserId($this->user->referralTree->parent->user->id);
                $depositService->setAmount($commission_amount);
                $depositService->setWalletName('Earning Wallet');

                $depositService->setDescription(serialize([
                    'description' => 'Commission # ' . $this->getType() . $is_eligible_for_quick_start_bonus ? ' - Quick Start bonus ' : ''
                ]));
                $depositService->setType('Commission');
                $depositService->setSubType('Direct Sell');
                $depositService->setServiceName('mlm');

                /** @var $commission CommissionModel */
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

    public function getType(): string
    {
        return DIRECT_SELL_COMMISSION;
    }
}
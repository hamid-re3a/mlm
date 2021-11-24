<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\ReferralTree;
use MLM\Services\CommissionResolver;
use User\Models\User;
use Wallets\Services\Grpc\Deposit;

class ResidualBonusCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;


    public function __construct(User $user)
    {
        $this->queue = env('QUEUE_RESIDUAL_NAME', 'mlm_residual');
        $this->user = $user;
    }

    public function handle()
    {
        if (!getSetting('RESIDUAL_BONUS_COMMISSION_IS_ACTIVE')) {
            return;
        }

        if (arrayHasValue(RESIDUAL_BONUS_COMMISSION, $this->user->deactivated_commission_types)) {
            return;
        }
        if ($this->user->commissions()
            ->where('type', RESIDUAL_BONUS_COMMISSION)
            ->whereDate('created_at', now()->toDate())->exists())
            return;
        $tree = ReferralTree::query()->where('user_id', $this->user->id)->first();
        $depth = $tree->_dpt;
        $commission_amount = (double)0;
        foreach ($this->user->residualBonusSetting as $residual_bonus_setting) {

            $users = ReferralTree::query()
                ->where('_dpt', $depth + $residual_bonus_setting->level)
                ->descendantsAndSelf($tree->id)

                ->pluck('user_id')->toArray();

            $descendants_commission = CommissionModel::query()->where('type', TRADING_PROFIT_COMMISSION)
                ->whereDate('created_at', now()->toDate())
                ->whereIn('user_id', $users)->sum('amount');

            $commission_amount += ($residual_bonus_setting->percentage / 100) * $descendants_commission;
        }


        /** @var $deposit_service_object  Deposit */
        $deposit_service_object = app(Deposit::class);
        $deposit_service_object->setUserId($this->user->id);
        $deposit_service_object->setAmount($commission_amount);
        $deposit_service_object->setWalletName(\Wallets\Services\Grpc\WalletNames::JANEX);

        $deposit_service_object->setDescription(serialize([
            'description' => 'Commission # ' . RESIDUAL_BONUS_COMMISSION
        ]));
        $deposit_service_object->setType('Commission');
        $deposit_service_object->setSubType('Residual Bonus');


        (new CommissionResolver)->payCommission($deposit_service_object, $this->user, RESIDUAL_BONUS_COMMISSION);


    }
}

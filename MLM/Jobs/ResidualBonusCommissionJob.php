<?php

namespace MLM\Jobs;

use App\Jobs\Wallet\WalletDepositJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\Commission as CommissionModel;
use MLM\Models\ReferralTree;
use User\Models\User;
use Wallets\Services\Grpc\Deposit;

class ResidualBonusCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        if ($this->user->commissions()
            ->where('type', RESIDUAL_BONUS_COMMISSION)
            ->whereDate('created_at', now()->toDate())->exists())
            return;

        $tree = ReferralTree::withDepth()->where('user_id', $this->user->id)->first();
        $depth = $tree->depth;
        $commission_amount = (double)0;
        foreach ($this->user->residualBonusSetting as $residual_bonus_setting) {

            $this->user->$residual_bonus_setting['level'];
            $users = ReferralTree::withDepth()->descendantsAndSelf($tree->id)
                ->where('depth', $depth + $residual_bonus_setting->level)
                ->pluck('user_id')->toArray();

            $descendants_commission = CommissionModel::query()->where('type', TRADING_PROFIT_COMMISSION)
                ->whereDate('created_at', now()->toDate())
                ->whereIn('user_id', $users)->sum('amount');

            $commission_amount += ($residual_bonus_setting->percentage / 100) * $descendants_commission;
        }




        /** @var $depositService  Deposit */
        $depositService = app(Deposit::class);
        $depositService->setUserId($this->user->id);
        $depositService->setAmount($commission_amount);
        $depositService->setWalletName(\Wallets\Services\Grpc\WalletNames::EARNING);

        $depositService->setDescription(serialize([
            'description' => 'Commission # ' . RESIDUAL_BONUS_COMMISSION
        ]));
        $depositService->setType('Commission');
        $depositService->setSubType('Trading Profit');
        $depositService->setServiceName('mlm');

        /** @var $commission CommissionModel */
        $commission = $this->user->commissions()->create([
            'amount' => $commission_amount,
            'type' => RESIDUAL_BONUS_COMMISSION,
        ]);


        if ($commission) {
            $depositService->setPayloadId($commission->id);
            WalletDepositJob::dispatch($depositService)->onConnection('rabbit')->onQueue('subscriptions');
        }


    }
}

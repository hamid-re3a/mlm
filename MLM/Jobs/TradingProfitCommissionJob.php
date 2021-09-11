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
use MLM\Models\PackageRoi;
use MLM\Services\PackageRoiService;
use MLM\Services\PackageService;
use Wallets\Services\Deposit;

class TradingProfitCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ordered_package;


    public function __construct(OrderedPackage $ordered_package)
    {
        $this->ordered_package = $ordered_package;
    }

    public function handle(PackageService $package_service)
    {
        if ($this->ordered_package->commissions()->where('type', TRADING_PROFIT_COMMISSION)
            ->whereDate('created_at', now()->toDate())->exists())
            return;

        if (!$this->ordered_package->active()->exists())
            return;


        $package = $package_service->findPackageByShortName($this->ordered_package->short_name);

        /** @var  $roi PackageRoi*/
        $roi = $package->rois()->today()->first();
        if($roi){


            $commission_amount = ($roi->roi_percentage / 100) * $this->ordered_package->price;


            /** @var $depositService  Deposit */
            $depositService = app(Deposit::class);
            $depositService->setUserId($this->ordered_package->user->id);
            $depositService->setAmount($commission_amount);
            $depositService->setWalletName('Earning Wallet');

            $depositService->setDescription(serialize([
                'description' => 'Commission # ' . TRADING_PROFIT_COMMISSION
            ]));
            $depositService->setType('Commission');
            $depositService->setSubType('Trading Profit');
            $depositService->setServiceName('mlm');

            /** @var $commission CommissionModel */
            $commission = $this->ordered_package->user->commissions()->create([
                'amount' => $commission_amount,
                'ordered_package_id' => $this->ordered_package->id,
                'type' => TRADING_PROFIT_COMMISSION,
            ]);

            if ($commission) {
                $depositService->setPayloadId($commission->id);
                WalletDepositJob::dispatch($depositService)->onConnection('rabbit')->onQueue('subscriptions');
            }


        }



    }
}

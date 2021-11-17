<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\OrderedPackage;
use MLM\Models\PackageRoi;
use MLM\Services\CommissionResolver;
use MLM\Services\PackageService;
use Wallets\Services\Grpc\Deposit;

class TradingProfitCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $ordered_package;


    public function __construct(OrderedPackage $ordered_package)
    {
        $this->queue = env('QUEUE_ROI_NAME','mlm_roi');
        $this->ordered_package = $ordered_package;
    }

    public function handle(PackageService $package_service)
    {
        if(!getSetting('TRADING_PROFIT_COMMISSION_IS_ACTIVE')){
            return ;
        }

        if (arrayHasValue(TRADING_PROFIT_COMMISSION, $this->ordered_package->user->deactivated_commission_types)) {
            return;
        }
        if (!$this->ordered_package->active()->exists() || $this->ordered_package->isSpecialPackage() || $this->ordered_package->isCompanyPackage() || !$this->ordered_package->canGetCommission())
            return;

        if (!$this->ordered_package->commissions()
            ->where('type', TRADING_PROFIT_COMMISSION)
            ->whereDate('created_at', now()->toDate())
            ->where('ordered_package_id', $this->ordered_package->id)->exists()) {


            /** @var  $roi PackageRoi */
            $roi = $this->ordered_package->package->rois()->today()->first();
            if ($roi) {
                $percentage = $roi->roi_percentage;

                $commission_amount = ($percentage / 100) * $this->ordered_package->price;


                /** @var $deposit_service_object  Deposit */
                $deposit_service_object = app(Deposit::class);
                $deposit_service_object->setUserId($this->ordered_package->user->id);
                $deposit_service_object->setAmount($commission_amount);
                $deposit_service_object->setWalletName(\Wallets\Services\Grpc\WalletNames::JANEX);

                $deposit_service_object->setDescription(serialize([
                    'description' => 'Commission # ' . TRADING_PROFIT_COMMISSION
                ]));
                $deposit_service_object->setType('Commission');
                $deposit_service_object->setSubType('Trading Profit');


                (new CommissionResolver)->payCommission($deposit_service_object, $this->ordered_package->user, TRADING_PROFIT_COMMISSION, $this->ordered_package->id);


            }
        }
    }
}

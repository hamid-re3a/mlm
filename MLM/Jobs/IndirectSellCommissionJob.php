<?php

namespace MLM\Jobs;

use App\Jobs\Wallet\WalletDepositJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MLM\Models\OrderedPackage;
use MLM\Models\OrderedPackagesIndirectCommission;
use User\Models\User;
use Wallets\Services\Grpc\Deposit;

class IndirectSellCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $package;
    private $user;
    private $level;


    public function __construct(User $user, OrderedPackage $package, $level = 0)
    {
        $this->package = $package;
        $this->user = $user;
        $this->level = $level;
    }


    public function handle()
    {

        if (is_null($this->user->referralTree->parent) || is_null($this->user->referralTree->parent->user))
            return;
        $biggest_active_package = $this->user->referralTree->parent->user->biggestActivePackage();
        if ($biggest_active_package) {
            /** @var  $indirect_found OrderedPackagesIndirectCommission */
            $indirect_found = $biggest_active_package->indirectCommission()->where('level', $this->level)->first();
            if ($indirect_found) {
                $commission_amount = ($this->package->price * $indirect_found->percentage / 100);

                /** @var $depositService  Deposit*/
                $depositService = app(Deposit::class);
                $depositService->setUserId($this->user->referralTree->parent->user->id);
                $depositService->setAmount($commission_amount);
                $depositService->setWalletName(\Wallets\Services\Grpc\WalletNames::EARNING);

                $depositService->setDescription(serialize([
                    'description' => 'Commission # ' . $this->getType()
                ]));
                $depositService->setType('Commission');
                $depositService->setSubType('Indirect Sell');
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

        if ($this->level == 7)
            return;
        IndirectSellCommissionJob::dispatch($this->user->referralTree->parent->user, $this->package, ++$this->level);

    }

    public function getType(): string
    {
        return INDIRECT_SELL_COMMISSION;
    }

}

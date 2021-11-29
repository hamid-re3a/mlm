<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use MLM\Models\OrderedPackage;
use MLM\Models\OrderedPackagesIndirectCommission;
use MLM\Services\CommissionResolver;
use User\Models\User;
use User\Services\UserService;
use Wallets\Services\Grpc\Deposit;

class IndirectSellCommissionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $package;
    private $user;
    private $level;


    public function __construct(User $user, OrderedPackage $package, $level = 0)
    {
        $this->queue = env('QUEUE_COMMISSIONS_NAME','mlm_commissions');
        $this->package = $package;
        $this->user = $user;
        $this->level = $level;
    }


    public function handle(UserService $user_service)
    {
        if(!getSetting('INDIRECT_SELL_COMMISSION_IS_ACTIVE')){
            return ;
        }

        if (is_null($this->user->referralTree->parent) || is_null($this->user->referralTree->parent->user_id))
            return;
        $parent = $user_service->findByIdOrFail($this->user->referralTree->parent->user_id);

        if (arrayHasValue(INDIRECT_SELL_COMMISSION, $parent->deactivated_commission_types)) {
            if ($this->level == 9)
                return;
            IndirectSellCommissionJob::dispatch($parent, $this->package, ++$this->level);
            return ;
        }
        $biggest_active_package = $parent->biggestActivePackage();
        if ($biggest_active_package) {

            /** @var  $indirect_found OrderedPackagesIndirectCommission */
            $indirect_found = $biggest_active_package->indirectCommission()->where('level', $this->level)->first();
            if ($indirect_found) {
                $commission_amount = ($this->package->price * $indirect_found->percentage / 100);

                /** @var $deposit_service_object  Deposit*/
                $deposit_service_object = app(Deposit::class);
                $deposit_service_object->setUserId($parent->id);
                $deposit_service_object->setAmount($commission_amount);
                $deposit_service_object->setWalletName(\Wallets\Services\Grpc\WalletNames::EARNING);

                $deposit_service_object->setDescription(serialize([
                    'description' => 'Commission # ' . $this->getType()
                ]));
                $deposit_service_object->setType('Commission');
                $deposit_service_object->setSubType('Indirect Sale');



                (new CommissionResolver)->payCommission($deposit_service_object,$parent,$this->getType(),$biggest_active_package->id, $this->package->id);

            }
        }

        if ($this->level == 9)
            return;
        IndirectSellCommissionJob::dispatch($parent, $this->package, ++$this->level);

    }

    public function getType(): string
    {
        return INDIRECT_SELL_COMMISSION;
    }



}

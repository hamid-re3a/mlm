<?php

namespace MLM\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use MLM\Models\OrderedPackage;
use MLM\Models\Rank;
use MLM\Services\CommissionResolver;
use User\Models\User;
use User\Services\UserService;
use Wallets\Services\Grpc\Deposit;

class BinaryCommissionJob implements ShouldQueue
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
        if (!getSetting('BINARY_COMMISSION_IS_ACTIVE')) {
            return;
        }

        if (!is_null($this->user->binaryTree->parent) && !is_null($this->user->binaryTree->parent->user_id)) {
            $parent = $user_service->findByIdOrFail($this->user->binaryTree->parent->user_id);

            if (arrayHasValue(BINARY_COMMISSION, $parent->deactivated_commission_types)) {
                BinaryCommissionJob::dispatch($parent, $this->package);
                return;
            }
            if ($parent->hasCompletedBinaryLegs()) {

                $biggest_active_package = $parent->biggestActivePackage();
                if ($biggest_active_package) {
                    $left_binary_children_price = $parent->binaryTree->leftSideChildrenPackagePrice();
                    $right_binary_children_price = $parent->binaryTree->rightSideChildrenPackagePrice();
                    $weaker_leg_price = $left_binary_children_price >= $right_binary_children_price ? $right_binary_children_price : $left_binary_children_price;
                    $requested_commission_amount = (int)$weaker_leg_price - (int)$parent->binaryTree->converted_points;

                    if ($requested_commission_amount > 0) {

                        /** @var $rank Rank */
                        $rank = getAndUpdateUserRank($parent);

                        $amount_paid_today = $parent->commissions()->where('type', $this->getType())
                            ->whereDate('created_at', now()->toDate())->sum('amount');


                        $rank_based_on_converted_points = userRankBasedOnConvertedPoint($weaker_leg_price);

                        DB::beginTransaction();
                        try {

                            if (!$this->package->isCompanyPackage() && ($requested_commission_amount + $amount_paid_today) > $rank_based_on_converted_points->cap)
                                $payable_amount = $this->payTheCap($rank_based_on_converted_points, $amount_paid_today, $requested_commission_amount, $parent, $biggest_active_package);
                            else
                                $payable_amount = $requested_commission_amount;


                            if ($payable_amount > 0) {
                                $commission_amount = $payable_amount * $biggest_active_package->binary_percentage / 100;

                                /** @var $deposit_service_object  Deposit */
                                $deposit_service_object = app(Deposit::class);
                                $deposit_service_object->setUserId($parent->id);
                                $deposit_service_object->setAmount($commission_amount);
                                $deposit_service_object->setWalletName(\Wallets\Services\Grpc\WalletNames::EARNING);

                                $deposit_service_object->setDescription(serialize([
                                    'description' => 'Commission # ' . $this->getType()
                                ]));
                                $deposit_service_object->setType('Commission');
                                $deposit_service_object->setSubType('Binary');
                                (new CommissionResolver)->payCommission($deposit_service_object, $parent, $this->getType(), $biggest_active_package->id, $this->package->id);
                            }


                            $parent->binaryTree->converted_points = $weaker_leg_price;
                            $parent->binaryTree->save();

                            DB::commit();
                        } catch (\Exception $exception) {
                            DB::rollBack();
                            throw $exception;
                        }
                    }
                }
            }
            BinaryCOmmissionJob::dispatch($parent, $this->package);
        }
    }

    public function getType(): string
    {
        return BINARY_COMMISSION;
    }

    /**
     * @param Rank $rank_based_on_converted_points
     * @param $amount_paid_today
     * @param int $requested_commission_amount
     * @param User $parent
     * @param OrderedPackage|null $biggest_active_package
     * @return int
     */
    private function payTheCap(Rank $rank_based_on_converted_points, $amount_paid_today, int $requested_commission_amount, User $parent, ?OrderedPackage $biggest_active_package): int
    {
        $payable_amount = $rank_based_on_converted_points->cap - $amount_paid_today;
        $payable_amount = ($payable_amount < 0) ? 0 : $payable_amount;


        $cap_amount = $requested_commission_amount - $payable_amount;

        $parent->commissions()->create([
            'amount' => $cap_amount * $biggest_active_package->binary_percentage / 100,
            'ordered_package_id' => $biggest_active_package->id,
            'type' => 'cap-commission',
        ]);
        return $payable_amount;
    }

}

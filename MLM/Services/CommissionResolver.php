<?php


namespace MLM\Services;


use Illuminate\Support\Facades\DB;
use MLM\Services\Wallet\WalletClientFacade;
use User\Models\User;
use Wallets\Services\Grpc\Deposit;

class CommissionResolver
{
    /**
     * @param Deposit $deposit_service_object
     * @param User $user
     * @param $type
     * @param null $package_id
     * @throws \Exception
     */
    public function payCommission(Deposit $deposit_service_object, User $user, $type, $package_id = null): void
    {
        DB::beginTransaction();
        try {
            $commission = $user->commissions()->create([
                'amount' => $deposit_service_object->getAmount(),
                'ordered_package_id' => $package_id,
                'type' => $type,
            ]);
            if ($commission) {
                $deposit_response = WalletClientFacade::deposit($deposit_service_object);
                $commission->transaction_id = $deposit_response->getTransactionId();
                $commission->save();
            } else {
                throw new \Exception('Commission Failed Error');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception('Commission Error => ' . $exception->getMessage());
        }

        DB::commit();

    }
}

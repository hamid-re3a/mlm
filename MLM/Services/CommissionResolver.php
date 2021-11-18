<?php


namespace MLM\Services;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MLM\Jobs\Emails\EmailJob;
use MLM\Mail\UserCommissionEmail;
use MLM\Models\Commission;
use Wallets\Services\WalletClientFacade;
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
            /** @var  $commission Commission */
            $commission = $user->commissions()->create([
                'amount' => $deposit_service_object->getAmount(),
                'ordered_package_id' => $package_id,
                'type' => $type,
            ]);
            if ($commission) {
                $deposit_response = WalletClientFacade::deposit($deposit_service_object);
                $commission->transaction_id = $deposit_response->getTransactionId();
                $commission->save();
                $this->notifyCommissionByEmail($deposit_service_object, $user, $commission);
            } else {
                throw new \Exception('Commission Failed Error');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::info('Commission Error => ' . $exception->getMessage());
            throw new \Exception('Commission Error => ' . $exception->getMessage());
        }

        DB::commit();

    }


    private function notifyCommissionByEmail(Deposit $deposit_service_object, User $user, Commission $commission): void
    {
        try {
            $type = $deposit_service_object->getType() . ' ' . $deposit_service_object->getSubType();
            EmailJob::dispatch(new UserCommissionEmail($user, $commission, $type),$user->email);
        } catch (\Throwable $exception) {
            Log::info('Commission email is not sent because => ' . $exception->getMessage());
        }
    }
}

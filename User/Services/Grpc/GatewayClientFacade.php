<?php


namespace User\Services\Grpc;

use Illuminate\Support\Facades\Facade;

/**
 * @method static User getUserById(Id $id )
 * @method static User getUserByMemberId(Id $id )
 * @method static WalletInfo getUserWalletInfo(WalletRequest $walletRequest)
 * @method static Acknowledge checkTransactionPassword(UserTransactionPassword $userTransactionPassword)
 */

class GatewayClientFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return __CLASS__;
    }

    public static function shouldProxyTo($class)
    {
        return app()->singleton(self::getFacadeAccessor(),$class);
    }
}

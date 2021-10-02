<?php


namespace MLM\Services\Wallet;


use Illuminate\Support\Facades\Facade;
use Wallets\Services\Grpc\Deposit;
use Wallets\Services\Grpc\Transfer;
use Wallets\Services\Grpc\Wallet;
use Wallets\Services\Grpc\Withdraw;

/**
 * @method static Deposit deposit(Deposit $argument)
 * @method static Withdraw withdraw(Withdraw $argument)
 * @method static Transfer transfer(Transfer $argument)
 * @method static Wallet getBalance(Wallet $argument)
 */
class WalletClientFacade extends Facade
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

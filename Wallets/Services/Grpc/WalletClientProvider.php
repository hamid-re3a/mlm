<?php


namespace Wallets\Services\Grpc;


use Wallets\Services\Grpc\Deposit;
use Wallets\Services\Grpc\Transfer;
use Wallets\Services\Grpc\Wallet;
use Wallets\Services\Grpc\Withdraw;

class WalletClientProvider
{
    protected static $client;

    public function __construct()
    {
        self::$client = getWalletGrpcClient();
    }

    public static function deposit(Deposit $argument): Deposit
    {
        /** @var $submit_response Deposit */
        list($submit_response, $flag) = self::$client->deposit($argument)->wait();
        if ($flag->code != 0)
            throw new \Exception('Wallet not responding', 406);
        return $submit_response;
    }

    public static function withdraw(Withdraw $argument): Withdraw
    {
        /** @var $submit_response Withdraw */
        list($submit_response, $flag) = self::$client->withdraw($argument)->wait();
        if ($flag->code != 0)
            throw new \Exception('Wallet not responding', 406);
        return $submit_response;
    }

    public static function transfer(Transfer $argument): Transfer
    {
        /** @var $submit_response Transfer */
        list($submit_response, $flag) = self::$client->transfer($argument)->wait();
        if ($flag->code != 0)
            throw new \Exception('Wallet not responding', 406);
        return $submit_response;
    }

    public static function getBalance(Wallet $argument): Wallet
    {
        /** @var $submit_response Wallet */
        list($submit_response, $flag) = self::$client->getBalance($argument)->wait();
        if ($flag->code != 0)
            throw new \Exception('Wallet not responding', 406);
        return $submit_response;
    }


}

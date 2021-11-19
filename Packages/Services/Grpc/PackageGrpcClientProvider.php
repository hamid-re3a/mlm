<?php


namespace Packages\Services\Grpc;

class PackageGrpcClientProvider
{
    protected static $client;

    public function __construct()
    {
        self::$client = getPackageGrpcClient();
    }

    public static function packageById(Id $id) : Package
    {
        /** @var $submit_response Package */
        list($submit_response, $flag) = self::$client->packageById($id)->wait();
        if ($flag->code != 0)
            throw new \Exception('Subscription not responding', 406);
        return $submit_response;
    }

    public static function packageIsInBiggestPackageCategory(PackageCheck $packages) : Acknowledge
    {
        /** @var $submit_response Acknowledge */
        list($submit_response, $flag) = self::$client->packageIsInBiggestPackageCategory($packages)->wait();
        if ($flag->code != 0)
            throw new \Exception('Subscription not responding', 406);
        return $submit_response;
    }


}

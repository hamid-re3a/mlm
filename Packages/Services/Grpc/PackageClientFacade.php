<?php


namespace Packages\Services\Grpc;


use Illuminate\Support\Facades\Facade;

/**
 * @method static Package packageById(Id $id)
 * @method static Acknowledge packageIsInBiggestPackageCategory(PackageCheck $packages)
 */

class PackageClientFacade extends Facade
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

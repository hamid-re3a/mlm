<?php


namespace MLM\Services;


use MLM\Models\OrderedPackage;
use MLM\Repository\OrderedPackageRepository;
use MLM\Repository\PackageRepository;
use Orders\Services\Grpc\Order;
use Packages\Services\Grpc\Id;
use Packages\Services\Grpc\Package;
use User\Models\User;

class OrderedPackageService
{

    /**
     * @var OrderedPackageRepository
     */
    private $ordered_package_repository;
    private $package_repository;

    public function __construct(OrderedPackageRepository $ordered_package_repository, PackageRepository $package_repository)
    {
        $this->ordered_package_repository = $ordered_package_repository;
        $this->package_repository = $package_repository;
    }

    public function updateOrderAndPackage(Order $order) : OrderedPackage
    {
        $package = $this->updatePackage($order);
        return $this->ordered_package_repository->updateOrderAndPackage($order,$package);
    }

    /**
     * @param Order $order
     * @return Package
     * @throws \Exception
     */
    private function updatePackage(Order $order): Package
    {
        $id = new Id();
        $id->setId($order->getPackageId());

        /** @var $package Package */
        list($package, $status) = getPackageGrpcClient()->packageById($id)->wait();
        if ($status->code != 0)
            throw new \Exception('Not a valid package in order');

        $this->package_repository->updatePackage($package);
        return $package;
    }
}

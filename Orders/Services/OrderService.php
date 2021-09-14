<?php


namespace Orders\Services;


use MLM\Repository\OrderedPackageRepository;
use Orders\Services\Grpc\Order;

class OrderService
{

    /**
     * @var OrderedPackageRepository
     */
    private $package_repository;

    public function __construct(OrderedPackageRepository $package_repository)
    {
        $this->package_repository = $package_repository;
    }

    public function updateOrder(Order $order)
    {
        return $this->package_repository->updatePackage($order);
    }
}

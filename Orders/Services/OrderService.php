<?php


namespace Orders\Services;


use MLM\Repository\PackageRepository;

class OrderService
{

    /**
     * @var PackageRepository
     */
    private $package_repository;

    public function __construct(PackageRepository $package_repository)
    {
        $this->package_repository = $package_repository;
    }
    public function updateOrder(Order $order)
    {
        return $this->package_repository->updatePackage($order);
    }
}

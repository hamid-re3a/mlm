<?php


namespace MLM\Services;

use MLM\Models\Package;
use MLM\Repository\PackageRepository;

class PackageService
{
    private $package_repository;

    public function __construct(PackageRepository $package_repository)
    {
        $this->package_repository = $package_repository;
    }

    public function findPackageByShortName($short_name): ?Package
    {
        return $this->package_repository->findPackageByShortName($short_name);
    }

}

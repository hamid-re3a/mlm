<?php


namespace MLM\Services;

use MLM\Repository\PackageRoiRepository;

class PackageRoiService
{
    private $package_roi_repository;

    public function __construct(PackageRoiRepository $package_roi_repository)
    {
        $this->package_roi_repository = $package_roi_repository;
    }




}

<?php


namespace MLM\Services;

use MLM\Repository\PackageRoiRepository;
use MLM\Services\Grpc\PackageRoi;

class PackageRoiService
{
    private $package_roi_repository;

    public function __construct(PackageRoiRepository $package_roi_repository)
    {
        $this->package_roi_repository = $package_roi_repository;
    }


    public function store(PackageRoi $packageRoi)
    {

        return $this->package_roi_repository->create($packageRoi);

    }

    public function update(PackageRoi $packageRoi)
    {

        return $this->package_roi_repository->update($packageRoi);

    }

    public function destroy(PackageRoi $packageRoi)
    {

        $this->package_roi_repository->delete($packageRoi);

    }

    public function getAll()
    {
        return $this->package_roi_repository->getAll();

    }

    public function getByPackageIdDueDate($packageId,$dueDate)
    {

        return $this->package_roi_repository->getByPackageIdDueDate($packageId,$dueDate);

    }


}

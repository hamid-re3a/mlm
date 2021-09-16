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
    public function bulkUpdate(PackageRoi $packageRoi)
    {

        return $this->package_roi_repository->update($packageRoi);

    }

    public function destroy(int $packageId,string $dueDate)
    {

        $this->package_roi_repository->delete($packageId,$dueDate);

    }

    public function getAll()
    {
        return $this->package_roi_repository->getAll();

    }

    public function getByPackageIdDueDate(int $packageId,string $dueDate)
    {

        return $this->package_roi_repository->getByPackageIdDueDate($packageId,$dueDate);

    }


}

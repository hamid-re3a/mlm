<?php


namespace MLM\Services;


use MLM\Repository\ResidualBonusSettingRepository;
use MLM\Services\Grpc\ResidualBonus;

class ResidualBonusSettingService
{
    private $package_roi_repository;

    public function __construct(ResidualBonusSettingRepository $package_roi_repository)
    {
        $this->package_roi_repository = $package_roi_repository;
    }


    public function store(ResidualBonus $residualBonus)
    {

        return $this->package_roi_repository->create($residualBonus);

    }

    public function update(ResidualBonus $residualBonus)
    {

        return $this->package_roi_repository->update($residualBonus);

    }
    public function bulkUpdate(ResidualBonus $residualBonus)
    {

        return $this->package_roi_repository->update($residualBonus);

    }

    public function destroy(int $id)
    {

        $this->package_roi_repository->delete($id);

    }

    public function getAll()
    {
        return $this->package_roi_repository->getAll();

    }

    public function getById(int $id)
    {

        return $this->package_roi_repository->getById($id);

    }


}

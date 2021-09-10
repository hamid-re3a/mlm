<?php


namespace MLM\Repository;

use MLM\Models\Package;
use Packages\Services\Package as PackageData;

class PackageRepository
{
    protected $entity_name = Package::class;

    public function create(PackageData $package)
    {
        $package_entity = new $this->entity_name;
        $package_entity->name = $package->getName();
        $package_entity->roi_percentage = $package->getRoiPercentage();
        $package_entity->save();
        return $package_entity;
    }
    public function findPackageByShortName($short_name)
    {
        return app($this->entity_name)::query()->where('short_name',$short_name)->first();
    }
}

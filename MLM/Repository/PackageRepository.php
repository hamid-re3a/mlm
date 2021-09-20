<?php


namespace MLM\Repository;

use MLM\Models\OrderedPackage;
use MLM\Models\Package;
use Packages\Services\Grpc\Package as PackageGrpc;

class PackageRepository
{
    protected $entity_name = Package::class;
    /** @var  $package_instant Package */
    private $package_instant;

    public function __construct()
    {

        $this->package_instant = new $this->entity_name;
    }

    public function create(PackageGrpc $package)
    {
        $package_entity = new $this->entity_name;
        $package_entity->name = $package->getName();
        $package_entity->short_name = $package->getShortName();
        $package_entity->save();
        return $package_entity;
    }
    public function findPackageByShortName($short_name)
    {
        return app($this->entity_name)::query()->where('short_name',$short_name)->first();
    }

    public function updatePackage(PackageGrpc $package)
    {

        /** @var  $package_entity Package */
        $package_entity = new $this->entity_name;
        $package_find = $package_entity->query()->firstOrCreate(['id'=>$package->getId()]);
        $package_find->name = $package->getName();
        $package_find->short_name = $package->getShortName();
        $package_find->save();
        return $package_find;
    }

    public function packageExistsById(int $package_id)
    {
        return $this->package_instant->query()->where('id',$package_id)->exists();
    }

    public function findPackageById(int $package_id)
    {
        return $this->package_instant->query()->find($package_id);
    }



}

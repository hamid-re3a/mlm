<?php


namespace MLM\Repository;

use Illuminate\Support\Facades\Auth;
use MLM\Models\PackageRoi;
use MLM\Services\PackageRoiService as PackageRoiData;

class PackageRoiRepository
{
    protected $entity_name = PackageRoi::class;

    public function create($request)
    {
        $packageRio_entity = new $this->entity_name;
        $packageRio_entity->user_id=Auth::user()->id;
        $packageRio_entity->package_id=$request['package_id'];
        $packageRio_entity->roi_percentage=$request['roi_percentage'];
        $packageRio_entity->due_date=$request['due_date'];
        $packageRio_entity->save();
    }

}

<?php


namespace MLM\Repository;

use Illuminate\Support\Facades\Auth;
use MLM\Models\PackageRoi;
use MLM\Services\Grpc\PackageRoi as PackageRoiData;

class PackageRoiRepository
{
    protected $entity_name = PackageRoi::class;

    public function create(PackageRoiData $packageRoi)
    {
        $packageRoi_entity = new $this->entity_name;
        $packageRoi = $packageRoi_entity->create([
            "user_id" => Auth::user()->id,
            "package_id" => $packageRoi->getPackageId(),
            "roi_percentage" => $packageRoi->getRoiPercentage(),
            "due_date" => $packageRoi->getDueDate()
        ]);
        $packageRoi = $packageRoi->fresh();
        return $packageRoi;
    }

    public function update(PackageRoiData $packageRoi)
    {
        $packageRoi_entity = new $this->entity_name;

        $packageRoi_find = $packageRoi_entity->query()->firstOrCreate(
            [
                'package_id' => $packageRoi->getPackageId(),
                'due_date' => $packageRoi->getDueDate(),
            ],
            [
                'user_id' => Auth::user()->id
            ]
        );
        $packageRoi_find->update([
            "package_id" => $packageRoi->getPackageId(),
            "roi_percentage" => $packageRoi->getRoiPercentage(),
            "due_date" => $packageRoi->getDueDate()

        ]);

        return $packageRoi_find;

    }

    public function delete($id)
    {
        $packageRoi_entity = new $this->entity_name;
        $packageRoi_find = $packageRoi_entity->query()->find($id);
        $packageRoi_find->delete();

    }

    public function getAll()
    {
        $packageRoi_entity = new $this->entity_name;
        return $packageRoi_entity->query()->all();

    }

    public function getByPackageIdDueDate($packageId, $dueDate)
    {
        $packageRoi_entity = new $this->entity_name;

        return $packageRoi_entity->query()->where('package_id', $packageId)->where('due_date', $dueDate)->first();

    }

}

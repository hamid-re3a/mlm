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
        $packageRio_entity = new $this->entity_name;
        $packageRio = $packageRio_entity->create([
            "user_id" => Auth::user()->id,
            "package_id" => $packageRoi->getPackageId(),
            "roi_percentage" => $packageRoi->getRoiPercentage(),
            "due_date" => $packageRoi->getDueDate()
        ]);
        $packageRio = $packageRio->fresh();
        return $packageRio;
    }

    public function update(PackageRoiData $packageRoi)
    {
        $packageRio_entity = new $this->entity_name;

        $packageRoi_find = $packageRio_entity->query()->firstOrCreate(
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
        $packageRio_entity = new $this->entity_name;
        $packageRoi_find = $packageRio_entity->query()->find($id);
        $packageRoi_find->delete();

    }

    public function getAll()
    {
        $packageRio_entity = new $this->entity_name;
        return $packageRio_entity->query()->all();

    }

    public function getByPackageIdDueDate($packageId, $dueDate)
    {
        $packageRio_entity = new $this->entity_name;

        return $packageRio_entity->query()->where('package_id', $packageId)->where('due_date', $dueDate)->first();

    }

}

<?php


namespace MLM\Repository;

use Carbon\Carbon;
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

    public function delete(int $packageId,string $dueDate)
    {
        $packageRoi_entity = new $this->entity_name;
        $packageRoi_find = $packageRoi_entity->query()->where('package_id',$packageId)->where( 'due_date',$dueDate)->first();
        $packageRoi_find->delete();

    }

    public function getAll()
    {
        $packageRoi_entity = new $this->entity_name;

        return $packageRoi_entity->query()->get();

    }

    public function getByPackageIdDueDate(int $packageId, string $dueDate)
    {
        $packageRoi_entity = new $this->entity_name;

        return $packageRoi_entity->query()->where('package_id', $packageId)->where('due_date', $dueDate)->first();

    }

    public function getAllByDate($from_date = null, $to_date = null,$package_id = null)
    {
        /** @var  $packageRoi_entity PackageRoi*/
        $packageRoi_entity = new $this->entity_name;
        $query = $packageRoi_entity->query();
        if(!is_null($from_date))
            $query->whereDate('due_date' , '>=', Carbon::make($from_date)->toDate());
        if(!is_null($to_date))
            $query->whereDate('due_date' , '<=', Carbon::make($to_date)->toDate());
        if(!is_null($package_id))
            $query->where('package_id' , $package_id);
        return $query->get();
    }

}

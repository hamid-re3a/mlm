<?php

namespace MLM\Http\Controllers\Admin;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MLM\Http\Requests\PackageRoiBulkUpdateRequest;
use MLM\Http\Requests\PackageRoiDestroyRequest;
use MLM\Http\Requests\PackageRoiIndexRequest;
use MLM\Http\Requests\PackageRoiStoreRequest;
use MLM\Http\Requests\PackageRoiUpdateRequest;
use MLM\Http\Resources\PackageRoi\PackageRoiResource;
use MLM\Services\Grpc\PackageRoi;
use MLM\Services\PackageRoiService;
use Throwable;

/**
 * Class PackageRoiController
 * @package MLM\Http\Controllers\Admin
 */
class PackageRoiController extends Controller
{
    use  ValidatesRequests;

    /**
     * @var PackageRoiService
     */
    private $packageRoiService;

    /**
     * PackageRoiController constructor.
     * @param PackageRoiService $packageRoiService
     */
    public function __construct(PackageRoiService $packageRoiService)
    {
        $this->packageRoiService = $packageRoiService;
    }

    /**
     * get all PackageRois
     * @group Admin User > PackageRoi
     * @return JsonResponse
     */
    public function index(PackageRoiIndexRequest $request)
    {

        $rois = $this->packageRoiService->getAllByDate($request->from_date,$request->to_date,$request->package_id);

        if(is_null($rois))
            return api()->success(trans('responses.ok'),[]);

        return api()->success(trans('responses.ok'), PackageRoiResource::collection($rois));
    }

    /**
     * get PackageRoi by package_id and due_date
     * @group Admin User > PackageRoi
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @queryParam package_id
     * @queryParam due_date
     */
    public function show(Request $request)
    {
        $this->handleValidation($request->all());
        $packageRoi = $this->packageRoiService->getByPackageIdDueDate($request->package_id, $request->due_date);

        if(is_null($packageRoi)){
            return api()->success(trans('responses.ok'),[]);
        }

        return api()->success(trans('responses.ok'),  PackageRoiResource::make($packageRoi));

    }

    /**
     * store new package roi
     * @group Admin User > PackageRoi
     * @param PackageRoiStoreRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function store(PackageRoiStoreRequest $request)
    {
        $this->handleValidation($request->all());

        try {
            $packageRoi = $this->packageRoiService->store($this->PackageRoi($request->all()));

            return api()->success(trans('responses.ok'), $packageRoi);
        } catch (Throwable $e) {
            return api()->error($e->getMessage(), null);

        }
    }

    /**
     * update package roi
     * @group Admin User > PackageRoi
     * @param PackageRoiUpdateRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(PackageRoiUpdateRequest $request)
    {
        $this->handleValidation($request->all());
        try {
            $packageRoi = $this->packageRoiService->update($this->PackageRoi($request->all()));

            return api()->success(trans('responses.ok'), $packageRoi);
        } catch (Throwable $e) {
            return api()->error($e->getMessage(), null);

        }

    }

    /**
     * update a list of packageRois
     * @group Admin User > PackageRoi
     * @param PackageRoiBulkUpdateRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function bulkUpdate(PackageRoiBulkUpdateRequest $request)
    {
        $this->handleValidation($request->all(),true);

        try {
            $updatedPackageRois=[];
            foreach($request->package_id as $packageId){
                foreach ($request->due_date as $dueDate){
                    $packageRoi= $this->PackageRoi(['package_id'=>$packageId ,'due_date'=>$dueDate,'roi_percentage'=>$request->roi_percentage]);
                    $updatedPackageRoi=$this->packageRoiService->bulkUpdate($packageRoi);
                    $updatedPackageRois[]=$updatedPackageRoi;
                }
            }

            return api()->success(trans('responses.ok'),$updatedPackageRois);
        } catch (Throwable $e) {
            return api()->error($e->getMessage(), null);

        }

    }

    /**
     * delete packageRoi by package_id and due_date
     * @group Admin User > PackageRoi
     * @param PackageRoiDestroyRequest $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function destroy(PackageRoiDestroyRequest $request)
    {
        $this->handleValidation($request->all());

        try {
            $this->packageRoiService->destroy($request['package_id'], $request['due_date']);

            return api()->success(trans('responses.ok'));
        } catch (Throwable $e) {
            return api()->error($e->getMessage(), null);

        }

    }

    /**
     * @param $request
     * @return PackageRoi
     */
    private function PackageRoi(array $request)
    {

        $packageRoi = new PackageRoi();
        $packageRoi->setPackageId($request['package_id']);
        $packageRoi->setRoiPercentage($request['roi_percentage']);
        $packageRoi->setDueDate($request['due_date']);

        return $packageRoi;
    }


    /**
     * @param array $request
     * @param bool $bulk
     * @throws ValidationException
     */
    public function handleValidation(array $request, bool $bulk = null)
    {
        $field = $bulk ? 'package_id.*' : 'package_id';
        $validator = Validator::make($request, [
            $field => 'required|integer|exists:packages,id',
        ]);
        if ($validator->fails()) {
            throw (new ValidationException($validator));
        }

    }


}

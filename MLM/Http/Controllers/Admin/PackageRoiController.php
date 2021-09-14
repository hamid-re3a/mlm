<?php

namespace MLM\Http\Controllers\Admin;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use MLM\Http\Requests\PackageRoiStoreRequest;
use MLM\Http\Requests\PackageRoiUpdateRequest;
use MLM\Http\Resources\PackageRoi\PackageRoiResource;
use MLM\Services\Grpc\PackageRoi;
use MLM\Services\PackageRoiService;

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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $packageRois=PackageRoiResource::collection($this->packageRoiService->getAll());

        return api()->success(trans('responses.ok'), $packageRois);

    }

    /**
     * @group
     * Admin MLM > PackageRoi > show
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $packageRois=PackageRoiResource::collection($this->packageRoiService->getByPackageIdDueDate($request->package_id,$request->due_date));

        return api()->success(trans('responses.ok'), $packageRois);

    }

    /**
     * store new package roi
     * @group
     * Admin MLM > PackageRoi > create
     * @param PackageRoiStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PackageRoiStoreRequest $request)
    {
        try {
            $packageRoi = $this->packageRoiService->store($this->PackageRoi($request));

            return api()->success(trans('responses.ok'), $packageRoi);
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }
    }

    /**
     * update package roi
     * @group
     * Admin MLM > PackageRoi > update
     * @param PackageRoiUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PackageRoiUpdateRequest $request)
    {
        try {
            $packageRoi = $this->packageRoiService->update($this->PackageRoi($request));

            return api()->success(trans('responses.ok'), $packageRoi);
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }

    }

    /**
     * delete package roi
     * @group
     * Admin MLM > PackageRoi > delete
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
          $this->packageRoiService->destroy($request->id);

            return api()->success(trans('responses.ok'));
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }

    }


    /**
     * @param $request
     * @return PackageRoi
     */
    private function PackageRoi($request)
    {

        $packageRoi = new PackageRoi();
        $packageRoi->setPackageId($request['package_id']);
        $packageRoi->setRoiPercentage($request['roi_percentage']);
        $packageRoi->setDueDate($request['due_date']);

        return $packageRoi;
    }


}

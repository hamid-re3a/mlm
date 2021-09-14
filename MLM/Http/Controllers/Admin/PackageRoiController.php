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
    private $packageRioService;

    /**
     * PackageRoiController constructor.
     * @param PackageRoiService $packageRioService
     */
    public function __construct(PackageRoiService $packageRioService)
    {
        $this->packageRioService = $packageRioService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $packageRios=PackageRoiResource::collection($this->packageRioService->getAll());

        return api()->success(trans('responses.ok'), $packageRios);

    }

    /**
     * @group
     * Admin MLM > PackageRio > show
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $packageRios=PackageRoiResource::collection($this->packageRioService->getByPackageIdDueDate($request->package_id,$request->due_date));

        return api()->success(trans('responses.ok'), $packageRios);

    }

    /**
     * store new package rio
     * @group
     * Admin MLM > PackageRio > create
     * @param PackageRoiStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PackageRoiStoreRequest $request)
    {
        try {
            $packageRoi = $this->packageRioService->store($this->PackageRoi($request));

            return api()->success(trans('responses.ok'), $packageRoi);
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }
    }

    /**
     * update package rio
     * @group
     * Admin MLM > PackageRio > update
     * @param PackageRoiUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PackageRoiUpdateRequest $request)
    {
        try {
            $packageRoi = $this->packageRioService->update($this->PackageRoi($request));

            return api()->success(trans('responses.ok'), $packageRoi);
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }

    }

    /**
     * delete package rio
     * @group
     * Admin MLM > PackageRio > delete
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        try {
          $this->packageRioService->destroy($request->id);

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

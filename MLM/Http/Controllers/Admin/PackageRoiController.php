<?php

namespace MLM\Http\Controllers\Admin;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use MLM\Services\PackageRoiService;

class PackageRoiController extends Controller
{
    use  ValidatesRequests;

    private $packageRioService;

    public function __construct(PackageRoiService $packageRioService)
    {
        $this->packageRioService = $packageRioService;
    }

    public function index()
    {

    }

    public function show()
    {

    }

    public function store(PackageRoiStoreRequest $request)
    {
        try {
            $this->packageRioService->store($request);

            return api()->success(trans('responses.ok'), null);
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }
    }

    public function update()
    {

    }

    public function destroy()
    {

    }


}

<?php

namespace MLM\Http\Controllers\Admin;


use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use MLM\Http\Requests\ResidualBonusSettingDestroyRequest;
use MLM\Http\Requests\ResidualBonusSettingStoreRequest;
use MLM\Http\Requests\ResidualBonusSettingUpdateRequest;
use MLM\Http\Resources\ResidualBonusSetting\ResidualBonusSettingResource;
use MLM\Services\Grpc\ResidualBonus;
use MLM\Services\ResidualBonusSettingService;


class ResidualBonusSettingController extends Controller
{
    use  ValidatesRequests;


    /**
     * @var
     */
    private $residualBonusSettingService;

    /**
     * ResidualBonusSettingController constructor.
     * @param ResidualBonusSettingService $residualBonusSettingService
     */
    public function __construct(ResidualBonusSettingService $residualBonusSettingService)
    {
        $this->residualBonusService = $residualBonusSettingService;
    }

    /**
     * get all Residual Bonuses Settings
     * @group
     * Admin MLM > ResidualBonusSetting
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $residualBonusSettings= ResidualBonusSettingResource::collection($this->residualBonusService->getAll());

        return api()->success(trans('responses.ok'), $residualBonusSettings);

    }

    /**
     * get  Residual Bonus Setting by id
     * @group
     * Admin MLM > ResidualBonusSetting
     * @param Request $request
     * @queryParam id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request)
    {
        $residualBonusSetting = new ResidualBonusSettingResource($this->residualBonusService->getById($request->id));

        return api()->success(trans('responses.ok'), $residualBonusSetting);

    }

    /**
     * create new  Residual Bonuses Setting
     * @group
     * Admin MLM > ResidualBonusSetting
     * @param ResidualBonusSettingStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ResidualBonusSettingStoreRequest $request)
    {
        try {
            $residualBonusSetting = $this->residualBonusService->store($this->ResidualBonus($request->all()));

            return api()->success(trans('responses.ok'), $residualBonusSetting);
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }
    }

    /**
     * update Residual Bonuses Setting
     * @group
     * Admin MLM > ResidualBonusSetting
     * @param ResidualBonusSettingUpdateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ResidualBonusSettingUpdateRequest $request)
    {
        try {
            $residualBonusSetting = $this->residualBonusService->update($this->ResidualBonus($request->all()));

            return api()->success(trans('responses.ok'), $residualBonusSetting);
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }

    }

    /**
     * delete Residual Bonuses Setting
     * @group
     * Admin MLM > ResidualBonusSetting
     * @param ResidualBonusSettingDestroyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(ResidualBonusSettingDestroyRequest $request)
    {

        try {
            $this->residualBonusService->destroy($request->id);

            return api()->success(trans('responses.ok'));
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);

        }

    }

    /**
     * @param array $request
     * @return ResidualBonus
     */
    private function ResidualBonus(array $request)
    {

        $residualBonus = new ResidualBonus();
        if(isset($request['id'])){
            $residualBonus->setId($request['id']);
        }
        $residualBonus->setRank($request['rank']);
        $residualBonus->setLevel($request['level']);
        $residualBonus->setPercentage($request['percentage']);

        return $residualBonus;
    }



}

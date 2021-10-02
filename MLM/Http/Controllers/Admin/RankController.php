<?php

namespace MLM\Http\Controllers\Admin;


use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use MLM\Http\Requests\Admin\CreateRankRequest;
use MLM\Http\Requests\Admin\DeleteRankRequest;
use MLM\Http\Requests\Admin\ShowRankRequest;
use MLM\Http\Requests\Admin\UpdateRankRequest;
use MLM\Http\Resources\Rank\RankResource;
use MLM\Repository\RankService;

/**
 * Class RankController
 * @package MLM\Http\Controllers\Admin
 */
class RankController extends Controller
{

    /**
     * @var RankService
     */
    private $rankService;

    /**
     * PackageRoiController constructor.
     * @param RankService $rankService
     */
    public function __construct(RankService $rankService)
    {
        $this->rankService = $rankService;
    }

    /**
     * List ranks
     * @group Admin User > Ranks
     */
    public function index()
    {
        try{
            return api()->success(null,RankResource::collection($this->rankService->getAll()));
        } catch (\Throwable $exception) {
            return api()->error();
        }
    }

    /**
     * Get rank
     * @group Admin User > Ranks
     * @param ShowRankRequest $request
     * @return JsonResponse
     */
    public function show(ShowRankRequest $request)
    {
        try{
            return api()->success(null,RankResource::make($this->rankService->getById($request->get('id'))));
        } catch (\Throwable $exception) {
            return api()->error(null,[
                'message' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Create new rank
     * @group Admin User > Ranks
     * @param CreateRankRequest $request
     * @return JsonResponse
     */
    public function store(CreateRankRequest $request)
    {
        try {
            return api()->success(null,RankResource::make($this->rankService->create($request->all())));
        } catch (\Throwable $exception) {
            return api()->error(null,[
                'subject' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Update rank
     * @group Admin User > Ranks
     * @param UpdateRankRequest $request
     * @return JsonResponse
     */
    public function update(UpdateRankRequest $request)
    {
        try {
            return api()->success(null,RankResource::make($this->rankService->update($request->all())));
        } catch (\Throwable $exception) {
            return api()->error(null,[
                'subject' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Delete rank
     * @group Admin User > Ranks
     * @param DeleteRankRequest $request
     * @return JsonResponse
     */
    public function delete(DeleteRankRequest $request)
    {
        try {
            $this->rankService->delete($request->get('id'));

            return api()->success();
        } catch (\Throwable $exception) {
            return api()->error(null,[
                'subject' => $exception->getMessage()
            ]);
        }
    }


}

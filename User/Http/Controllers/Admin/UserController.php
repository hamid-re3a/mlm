<?php

namespace User\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use User\Http\Requests\Admin\AdminToggleCommissionRequest;
use User\Http\Requests\Admin\AdminUserInfoRequest;
use User\Http\Requests\Admin\UserListRequest;
use User\Http\Resources\Auth\ProfileResource;
use User\Models\User;
use User\Services\UserService;

class UserController extends Controller
{

    /**
     * Get user's list
     * @group
     * Admin > User
     * @param UserListRequest $request
     * @return JsonResponse
     */
    public function index(UserListRequest $request)
    {
        $list = User::query()->filter()->paginate();
        return api()->success(null, [
            'list' => ProfileResource::collection($list),
            'pagination' => [
                'total' => $list->total(),
                'per_page' => $list->perPage(),
            ]
        ]);
    }
    /**
     * User info
     * @group
     * Admin User > MLM Settings
     */
    public function userInfo(AdminUserInfoRequest $request, UserService $userService)
    {

        $user = User::query()->find(request('user_id'));
        try {
            return api()->success(trans('user.responses.user-info'), $user);
        } catch (\Throwable $e) {
            return api()->error($e->getMessage(), null);
        }

    }

    /**
     * Toggle Commission
     * @group
     * Admin User > MLM Settings
     */
    public function toggleCommission(AdminToggleCommissionRequest $request, UserService $userService)
    {

        $user = User::query()->find(request('user_id'));
        try {
            $deactivated_commission_types = $user->deactivated_commission_types;

            if (is_array($deactivated_commission_types) && $key = array_search(request('deactivated_commission_type'), $deactivated_commission_types) !== false) {
                unset($deactivated_commission_types[$key]);
            } else {
                $deactivated_commission_types[] = request('deactivated_commission_type');
                $user->deactivated_commission_types = $deactivated_commission_types;
            }

            $user->save();

            return api()->success(trans('user.responses.user-updated-successfully'), $user);

        } catch (\Throwable $e) {

            return api()->error($e->getMessage(), null);
        }

    }


}

<?php

namespace User\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use User\Http\Requests\Admin\AdminToggleCommissionRequest;
use User\Models\User;
use User\Services\UserService;

class UserController extends Controller
{

    /**
     * Toggle Commission
     * @group
     * Public User > MLM Settings
     */
    public function addCommissionToBlacklist(AdminToggleCommissionRequest $request, UserService $userService)
    {

        $user = User::query()->find(request('user_id'));
        try {
            $deactivated_commission_types = $user->deactivated_commission_types;
            if (($key = array_search(request('deactivated_commission_type'), $deactivated_commission_types)) !== false) {
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

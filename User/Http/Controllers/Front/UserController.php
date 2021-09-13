<?php

namespace User\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use User\Http\Requests\UserRequest;
use User\Services\UserService;

class UserController extends Controller
{
    /**
     * get count active package
     * @group
     * Public User > MLM > User Binary Position
     * @param UserRequest $request
     * @return JsonResponse
     */
    public function editBinaryPosition(UserRequest $request, UserService $userService)
    {
        try {
            $userService->editBinaryPosition($request);

            return api()->success(trans('user.responses.user-updated-successfully'), null);

        } catch (\Throwable $e) {

            return api()->error($e->getMessage(), null);
        }

    }


}

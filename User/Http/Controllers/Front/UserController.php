<?php

namespace User\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use User\Http\Requests\UserRequest;
use User\Services\UserService;

class UserController extends Controller
{

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

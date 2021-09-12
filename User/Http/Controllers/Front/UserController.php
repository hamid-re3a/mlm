<?php

namespace User\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use User\Http\Requests\UserRequest;
use User\Services\UserService;

class UserController extends Controller
{

    /**
     * Edit Binary Position
     * @group
     * Public User > MLM Settings
     */
    public function editBinaryPosition(UserRequest $request, UserService $userService)
    {
        try {
            $userService->editBinaryPosition(auth()->user()->id, request('default_binary_position'));

            return api()->success(trans('user.responses.user-updated-successfully'), null);

        } catch (\Throwable $e) {

            return api()->error($e->getMessage(), null);
        }

    }


}

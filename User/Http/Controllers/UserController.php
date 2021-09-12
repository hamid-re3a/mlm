<?php

namespace  User\Http\Controllers;

use Illuminate\Routing\Controller;
use User\Http\Requests\UserRequest;
use User\Services\UserService;

class UserController extends Controller
{

    public function editBinaryPosition(UserRequest $request ,UserService $userService)
    {
        try {
            //todo resource

            $userService->editBinaryPosition($request);
            return api()->success(trans('user.responses.user-updated-successfully'), null);

        }catch (\Throwable $e){

            return api()->success($e->getMessage(), null);
        }

    }



}

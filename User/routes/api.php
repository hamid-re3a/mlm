<?php

use Illuminate\Support\Facades\Route;
use User\Http\Controllers\UserController;


Route::middleware(['auth'])->name('users.')->group(function () {
//    Route::middleware(['role:super-admin', 'auth_user_mlm'])->group(function () {
        Route::put('edit_binary_position', [UserController::class, 'editBinaryPosition'])->name('binaryPosition');

//    });
});

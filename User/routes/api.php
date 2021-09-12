<?php

use Illuminate\Support\Facades\Route;
use User\Http\Controllers\Front\UserController;


//Route::middleware(['auth','role:'.USER_ROLE_SUPER_ADMIN])->name('users.')->group(function () {
//});

Route::middleware(['auth'])->name('users.')->group(function () {
    Route::put('edit_binary_position', [UserController::class, 'editBinaryPosition'])->name('binaryPosition');
});

<?php

use Illuminate\Support\Facades\Route;
use User\Http\Controllers\UserController;


Route::middleware(['auth_user_mlm','role:super-admin'])->name('users.')->group(function () {
    Route::put('edit_binary_position',[UserController::class,'editBinaryPosition'])->name('binaryPosition');

});

<?php

use Illuminate\Support\Facades\Route;
use User\Http\Controllers\Front\UserController;

Route::middleware(['auth'])->name('users.')->group(function () {
    Route::put('edit_binary_position', [UserController::class, 'editBinaryPosition'])->name('binaryPosition');
});

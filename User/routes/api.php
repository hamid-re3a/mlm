<?php

use Illuminate\Support\Facades\Route;
use User\Http\Controllers\Front\UserController;
use User\Http\Controllers\Admin\UserController as AdminUserController;

Route::middleware(['auth'])->name('users.')->group(function () {
    Route::put('edit_binary_position', [UserController::class, 'editBinaryPosition'])->name('binaryPosition');
});

Route::middleware(['role:' . USER_ROLE_SUPER_ADMIN . '|' . USER_ROLE_ADMIN_MLM])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['auth'])->name('users.')->group(function () {
        Route::put('toggle_commission', [AdminUserController::class, 'toggleCommission'])->name('toggleCommissionToBlacklist');
    });
});

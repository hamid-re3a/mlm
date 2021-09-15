<?php

use Illuminate\Support\Facades\Route;
use MLM\Http\Controllers\Admin\PackageRoiController;

Route::name('wallets.')->group(function () {

});


Route::middleware(['auth','role:'.USER_ROLE_SUPER_ADMIN.'|'.USER_ROLE_ADMIN_MLM])->prefix('packages_roi')->name('packagesRoi.')->group(function () {

    Route::get('/' ,[PackageRoiController::class, 'index'])->name('index');
    Route::get('/show' ,[PackageRoiController::class, 'show'])->name('show');
    Route::post('/' ,[PackageRoiController::class, 'store'])->name('store');
    Route::put('/' ,[PackageRoiController::class, 'update'])->name('update');
    Route::delete('/' ,[PackageRoiController::class, 'destroy'])->name('destroy');
});


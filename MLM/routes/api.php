<?php

use Illuminate\Support\Facades\Route;
use MLM\Http\Controllers\Admin\PackageRoiController;
use MLM\Http\Controllers\Admin\ResidualBonusSettingController;

Route::name('wallets.')->group(function () {

});


Route::middleware(['auth','role:'.USER_ROLE_SUPER_ADMIN.'|'.USER_ROLE_ADMIN_MLM])->prefix('packages_roi')->name('packagesRoi.')->group(function () {

    Route::get('/' ,[PackageRoiController::class, 'index'])->name('index');
    Route::get('/show' ,[PackageRoiController::class, 'show'])->name('show');
    Route::post('/' ,[PackageRoiController::class, 'store'])->name('store');
    Route::put('/' ,[PackageRoiController::class, 'update'])->name('update');
    Route::delete('/' ,[PackageRoiController::class, 'destroy'])->name('destroy');
    Route::put('/bulk_update' ,[PackageRoiController::class, 'bulkUpdate'])->name('bulkUpdate');


});


Route::middleware(['auth','role:'.USER_ROLE_SUPER_ADMIN])->prefix('residual_bonus_setting')->name('residualBonusSetting.')->group(function () {

    Route::get('/' ,[ResidualBonusSettingController::class, 'index'])->name('index');
    Route::get('/show' ,[ResidualBonusSettingController::class, 'show'])->name('show');
    Route::post('/' ,[ResidualBonusSettingController::class, 'store'])->name('store');
    Route::put('/' ,[ResidualBonusSettingController::class, 'update'])->name('update');
    Route::delete('/' ,[ResidualBonusSettingController::class, 'destroy'])->name('destroy');
});



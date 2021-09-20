<?php

use Illuminate\Support\Facades\Route;
use MLM\Http\Controllers\Admin\PackageRoiController;
use MLM\Http\Controllers\Admin\RankController;
use MLM\Http\Controllers\Admin\ResidualBonusSettingController;
use MLM\Http\Controllers\Admin\SettingController as AdminSettingController;

Route::middleware(['auth'])->group(function () {

    //Admin Routes
    Route::middleware(['role:' . USER_ROLE_SUPER_ADMIN . '|' . USER_ROLE_ADMIN_MLM])->prefix('admin')->name('admin.')->group(function () {

        Route::prefix('packages_roi')->name('packagesRoi.')->group(function () {
            Route::get('/', [PackageRoiController::class, 'index'])->name('index');
            Route::get('/show', [PackageRoiController::class, 'show'])->name('show');
            Route::post('/', [PackageRoiController::class, 'store'])->name('store');
            Route::put('/', [PackageRoiController::class, 'update'])->name('update');
            Route::delete('/', [PackageRoiController::class, 'destroy'])->name('destroy');
            Route::put('/bulk_update', [PackageRoiController::class, 'bulkUpdate'])->name('bulkUpdate');
        });

        Route::prefix('residual_bonus_setting')->name('residualBonusSetting.')->group(function () {
            Route::get('/', [ResidualBonusSettingController::class, 'index'])->name('index');
            Route::get('/show', [ResidualBonusSettingController::class, 'show'])->name('show');
            Route::post('/', [ResidualBonusSettingController::class, 'store'])->name('store');
            Route::put('/', [ResidualBonusSettingController::class, 'update'])->name('update');
            Route::delete('/', [ResidualBonusSettingController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('ranks')->name('ranks.')->group(function () {
            Route::get('', [RankController::class, 'index'])->name('index');
            Route::post('/show', [RankController::class, 'show'])->name('show');
            Route::post('', [RankController::class, 'store'])->name('store');
            Route::patch('', [RankController::class, 'update'])->name('update');
            Route::delete('', [RankController::class, 'delete'])->name('delete');
        });

        Route::name('settings.')->prefix('settings')->group(function () {
            Route::get('', [AdminSettingController::class, 'index'])->name('list');
            Route::patch('', [AdminSettingController::class, 'update'])->name('update');
        });
    });

    //Client routes
    Route::middleware(['role:' . USER_ROLE_CLIENT])->name('customer')->group(function () {
        Route::name('trees.')->prefix('trees')->group(function () {
            Route::get('binary', [\MLM\Http\Controllers\Front\TreeController::class, 'getBinaryTree'])->name('binary');
            Route::get('referral', [\MLM\Http\Controllers\Front\TreeController::class, 'getUserReferralTree'])->name('referral');
        });
    });

});



<?php

use App\Http\Controllers\LostReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('barang-hilang')->name('lost.')->group(function () {
    Route::get('/', [LostReportController::class, 'index'])
        ->name('index');

    Route::middleware('auth')->group(function () {
        Route::get('/tambah', [LostReportController::class, 'create'])
            ->name('create');

        Route::post('/', [LostReportController::class, 'store'])
            ->name('store');

        Route::get('/{lostItem}/ubah', [LostReportController::class, 'edit'])
            ->whereNumber('lostItem')
            ->name('edit');

        Route::put('/{lostItem}', [LostReportController::class, 'update'])
            ->whereNumber('lostItem')
            ->name('update');

        Route::delete('/{lostItem}', [LostReportController::class, 'destroy'])
            ->whereNumber('lostItem')
            ->name('destroy');
    });

    Route::get('/{lostItem}', [LostReportController::class, 'show'])
        ->whereNumber('lostItem')
        ->name('show');
});
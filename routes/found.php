<?php

use App\Http\Controllers\FoundReportController;
use Illuminate\Support\Facades\Route;

Route::prefix('barang-temuan')->name('found.')->group(function () {
    Route::get('/', [FoundReportController::class, 'index'])
        ->name('index');

    Route::middleware('auth')->group(function () {
        Route::get('/tambah', [FoundReportController::class, 'create'])
            ->name('create');

        Route::post('/', [FoundReportController::class, 'store'])
            ->name('store');

        Route::get('/{foundItem}/ubah', [FoundReportController::class, 'edit'])
            ->whereNumber('foundItem')
            ->name('edit');

        Route::put('/{foundItem}', [FoundReportController::class, 'update'])
            ->whereNumber('foundItem')
            ->name('update');

        Route::delete('/{foundItem}', [FoundReportController::class, 'destroy'])
            ->whereNumber('foundItem')
            ->name('destroy');
    });

    Route::get('/{foundItem}', [FoundReportController::class, 'show'])
        ->whereNumber('foundItem')
        ->name('show');
});

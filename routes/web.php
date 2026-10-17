<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FoundReportController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LostReportController;
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]+');
Route::pattern('category', '[0-9]+');
Route::pattern('user', '[0-9]+');

Route::get('/', [HomeController::class, 'index'])->name('home');

// ---- Auth ----
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login']);
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register']);
});
Route::post('/keluar', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// ---- CRUD laporan (barang hilang & barang temuan) ----
$reports = [
    'lost' => [LostReportController::class, 'barang-hilang'],
    'found' => [FoundReportController::class, 'barang-temuan'],
];
foreach ($reports as $key => [$controller, $slug]) {
    Route::get("/$slug", [$controller, 'index'])->name("$key.index");

    Route::middleware('auth')->group(function () use ($key, $controller, $slug) {
        Route::get("/$slug/tambah", [$controller, 'create'])->name("$key.create");
        Route::post("/$slug", [$controller, 'store'])->name("$key.store");
        Route::get("/$slug/{id}/ubah", [$controller, 'edit'])->name("$key.edit");
        Route::put("/$slug/{id}", [$controller, 'update'])->name("$key.update");
        Route::delete("/$slug/{id}", [$controller, 'destroy'])->name("$key.destroy");
    });

    Route::get("/$slug/{id}", [$controller, 'show'])->name("$key.show");
}

// ---- Admin ----
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('kategori', CategoryController::class)
        ->except(['create', 'show'])
        ->parameters(['kategori' => 'category'])
        ->names('categories');
    Route::get('/pengguna', [UserController::class, 'index'])->name('users.index');
    Route::delete('/pengguna/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

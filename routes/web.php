<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ForecastController;
use App\Http\Controllers\HasilPeramalanController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('transactions', TransactionController::class);
    Route::post('/forecasts/generate', [ForecastController::class, 'generate'])->name('forecasts.generate');
    Route::resource('forecasts', ForecastController::class);

    // Hasil Peramalan
    Route::get('/hasil-peramalan', [HasilPeramalanController::class, 'index'])->name('hasil-peramalan.index');
    Route::post('/hasil-peramalan/generate', [HasilPeramalanController::class, 'generate'])->name('hasil-peramalan.generate');
    Route::delete('/hasil-peramalan/destroy-filter', [HasilPeramalanController::class, 'destroy'])->name('hasil-peramalan.destroy-filter');
});

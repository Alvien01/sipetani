<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\HasilPeramalanController;
use App\Http\Controllers\StockRecommendationController;
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

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);

        // Products — custom routes HARUS sebelum resource
        Route::get('/products/export/csv',     [ProductController::class, 'exportCsv'])->name('products.export.csv');
        Route::get('/products/export/excel',   [ProductController::class, 'exportExcel'])->name('products.export.excel');
        Route::post('/products/import/csv',    [ProductController::class, 'importCsv'])->name('products.import.csv');
        Route::get('/products/template/csv',   [ProductController::class, 'templateCsv'])->name('products.template.csv');
        Route::resource('kategoris', \App\Http\Controllers\KategoriController::class);
        Route::resource('products', ProductController::class);

        // Hasil Peramalan (Holt-Winters)
        Route::get('/hasil-peramalan', [HasilPeramalanController::class, 'index'])->name('hasil-peramalan.index');
        Route::post('/hasil-peramalan/generate', [HasilPeramalanController::class, 'generate'])->name('hasil-peramalan.generate');
        Route::delete('/hasil-peramalan/destroy-filter', [HasilPeramalanController::class, 'destroyFilter'])->name('hasil-peramalan.destroy-filter');

        // Rekomendasi Stok
        Route::get('/stock-recommendations', [StockRecommendationController::class, 'index'])->name('stock-recommendations.index');
        Route::delete('/stock-recommendations/destroy', [StockRecommendationController::class, 'destroyFilter'])->name('stock-recommendations.destroy');
    });

    // Transactions — custom routes HARUS sebelum resource
    Route::get('/transactions/export/csv',   [TransactionController::class, 'exportCsv'])->name('transactions.export.csv');
    Route::get('/transactions/export/excel', [TransactionController::class, 'exportExcel'])->name('transactions.export.excel');
    Route::post('/transactions/import/csv',  [TransactionController::class, 'importCsv'])->name('transactions.import.csv');
    Route::get('/transactions/template/csv', [TransactionController::class, 'templateCsv'])->name('transactions.template.csv');
    Route::resource('transactions', TransactionController::class);
});

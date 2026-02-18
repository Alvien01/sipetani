<?php

use App\Http\Controllers\AlarmController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PersonelController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// Authentication Routes
Route::middleware(['guest'])->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth:personel'])->group(function () {
    Route::get('/alert', [AlarmController::class, 'showAlert'])->name('alert.view');
    Route::post('/alert/attend', [AlarmController::class, 'attend'])->name('alert.attend');
    Route::post('/fcm/subscribe', [AlarmController::class, 'subscribe'])->name('fcm.subscribe');

    // Settings Routes
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings/profile', [SettingController::class, 'updateProfile'])->name('settings.update-profile');
    Route::put('/settings/password', [SettingController::class, 'updatePassword'])->name('settings.update-password');
});

Route::middleware(['auth:personel', 'role:komandan'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('api/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::resource('personels', PersonelController::class);
    Route::post('/alarm/trigger', [AlarmController::class, 'trigger'])->name('alarm.trigger');
    Route::post('/alarm/stop', [AlarmController::class, 'stop'])->name('alarm.stop');
    Route::get('/reports/attendance', [AlarmController::class, 'showAttendanceReport'])->name('reports.attendance');
    Route::get('/api/reports/attendance/{alertId}', [AlarmController::class, 'getAttendanceReport'])->name('reports.get');
    Route::get('/api/reports/alerts', [AlarmController::class, 'getAlertsForReport'])->name('reports.alerts');
    Route::get('/reports/export/{alertId}', [AlarmController::class, 'exportAttendanceReport'])->name('reports.export');
});

Route::get('/test-sms', function() {
    $sms = new \App\Services\SmsService();
    $result = $sms->send('085790291176', 'SIAGA TK 1 - PERSIAPAN UPACARA');
    return $result ? 'SMS sent!' : 'SMS failed!';
})->middleware('auth:personel');

<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\VehicleController;
use App\Http\Middleware\EnsureAdminPanelEnabled;
use Illuminate\Support\Facades\Route;

// Administrator panel. There is no sign-up page: accounts are created in "Manajemen Admin".
Route::prefix('admin')->name('admin.')->middleware(EnsureAdminPanelEnabled::class)->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/masuk', [AuthController::class, 'create'])->name('login');
        Route::post('/masuk', [AuthController::class, 'store'])->middleware('throttle:30,1')->name('login.store');
    });

    Route::middleware(['auth', 'admin.active'])->group(function () {
        Route::post('/keluar', [AuthController::class, 'destroy'])->name('logout');

        Route::get('/', DashboardController::class)->name('dashboard');

        Route::prefix('kendaraan')->name('vehicles.')->group(function () {
            Route::get('/', [VehicleController::class, 'index'])->name('index');
            Route::get('/baru', [VehicleController::class, 'create'])->name('create');
            Route::post('/', [VehicleController::class, 'store'])->name('store');
            Route::get('/{vehicle}', [VehicleController::class, 'edit'])->name('edit');
            Route::put('/{vehicle}', [VehicleController::class, 'update'])->name('update');
            Route::delete('/{vehicle}', [VehicleController::class, 'destroy'])->name('destroy');
            Route::delete('/{vehicle}/foto/{side}', [VehicleController::class, 'destroyPhoto'])->name('photos.destroy');
        });

        Route::prefix('peminjaman')->name('bookings.')->group(function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
            Route::post('/{booking}/setujui', [BookingController::class, 'approve'])->name('approve');
            Route::post('/{booking}/tolak', [BookingController::class, 'reject'])->name('reject');
        });

        Route::prefix('pegawai')->name('employees.')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('index');
            Route::get('/baru', [EmployeeController::class, 'create'])->name('create');
            Route::post('/', [EmployeeController::class, 'store'])->name('store');
            Route::get('/{employee}', [EmployeeController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
            Route::patch('/{employee}/status', [EmployeeController::class, 'toggle'])->name('toggle');
            Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('admin')->name('admins.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/baru', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::patch('/{user}/status', [AdminUserController::class, 'toggle'])->name('toggle');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
        });
    });
});

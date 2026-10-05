<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NipVerificationController;
use App\Http\Controllers\PlaceController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/katalog', [VehicleController::class, 'index'])->name('katalog');

Route::get('/lokasi/cari', [PlaceController::class, 'search'])->middleware('throttle:60,1')->name('places.search');

Route::get('/kalender', [CalendarController::class, 'month'])->name('calendar.month');
Route::get('/kalender/detail', [CalendarController::class, 'day'])->name('calendar.day');

Route::prefix('peminjaman')->name('booking.')->group(function () {
    Route::get('/buat', [BookingController::class, 'create'])->name('create');
    Route::post('/verifikasi-nip', NipVerificationController::class)->middleware('throttle:10,1')->name('verify-nip');
    Route::post('/', [BookingController::class, 'store'])->name('store');
    Route::get('/{booking}/berhasil', [BookingController::class, 'success'])->name('success');
    Route::delete('/{booking}', [BookingController::class, 'cancel'])->name('cancel');
});

Route::prefix('status')->name('status.')->group(function () {
    Route::get('/', [StatusController::class, 'index'])->name('index');
    Route::post('/masuk', [StatusController::class, 'login'])->middleware('throttle:10,1')->name('login');
    Route::post('/keluar', [StatusController::class, 'logout'])->name('logout');
});

require __DIR__.'/admin.php';

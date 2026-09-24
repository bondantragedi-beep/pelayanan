<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Sekolah\DashboardController;
use App\Http\Controllers\Sekolah\KgbController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

// TODO: bungkus grup di bawah dengan middleware auth guard 'sekolah'
// begitu LoginController + guard sekolah selesai dibuat, contoh:
// Route::middleware('auth:sekolah')->prefix('sekolah')->name('sekolah.')->group(function () { ... });
Route::prefix('sekolah')->name('sekolah.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/layanan/kgb', [KgbController::class, 'create'])->name('kgb');
    Route::post('/layanan/kgb', [KgbController::class, 'store'])->name('kgb.store');
});

// Admin login memakai akun verifikator (NIP) yang sama, dengan hak akses tambahan.
// TODO: bungkus grup di bawah dengan middleware auth guard 'verifikator' + cek role admin,
// begitu guard verifikator selesai dibuat, contoh:
// Route::middleware(['auth:verifikator', 'role:admin'])->prefix('admin')->name('admin.')->group(function () { ... });
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/penugasan', [AdminController::class, 'penugasan'])->name('penugasan');
    Route::post('/penugasan/{npsn}', [AdminController::class, 'updatePenugasan'])->name('penugasan.update');
    Route::get('/data-masuk', [AdminController::class, 'dataMasuk'])->name('data-masuk');
});

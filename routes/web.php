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
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Semua route di bawah sudah dikunci: hanya sekolah yang login (guard 'sekolah')
// yang bisa mengaksesnya. Yang belum login akan otomatis dilempar ke /login.
Route::middleware('auth:sekolah')->prefix('sekolah')->name('sekolah.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/layanan/kgb', [KgbController::class, 'create'])->name('kgb');
    Route::post('/layanan/kgb', [KgbController::class, 'store'])->name('kgb.store');
});

// Admin login memakai akun verifikator (NIP) yang sama. Middleware 'auth:verifikator'
// memastikan sudah login sebagai verifikator, lalu middleware 'admin' (alias di
// bootstrap/app.php) memastikan akun tsb bertanda is_admin = true.
Route::middleware(['auth:verifikator', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/penugasan', [AdminController::class, 'penugasan'])->name('penugasan');
    Route::post('/penugasan/{npsn}', [AdminController::class, 'updatePenugasan'])->name('penugasan.update');
    Route::get('/data-masuk', [AdminController::class, 'dataMasuk'])->name('data-masuk');
});

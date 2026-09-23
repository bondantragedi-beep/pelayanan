<?php

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

<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Sekolah\BerkasController;
use App\Http\Controllers\Sekolah\DashboardController;
use App\Http\Controllers\Sekolah\KgbController;
use App\Http\Controllers\Verifikator\BerkasController as VerifikatorBerkasController;
use App\Http\Controllers\Verifikator\DashboardController as VerifikatorDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

// Dikunci: hanya sekolah yang login (guard 'sekolah') yang bisa mengakses.
Route::middleware('auth:sekolah')->prefix('sekolah')->name('sekolah.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/layanan/kgb', [KgbController::class, 'create'])->name('kgb');
    Route::post('/layanan/kgb', [KgbController::class, 'store'])->name('kgb.store');
    Route::get('/berkas', [BerkasController::class, 'index'])->name('berkas');
    Route::get('/inbox', [BerkasController::class, 'inbox'])->name('inbox');
});

// Dikunci: verifikator biasa maupun admin sama-sama login lewat guard 'verifikator'.
// Verifikator biasa berhenti di sini; admin diarahkan lebih lanjut ke grup /admin di bawah.
Route::middleware('auth:verifikator')->prefix('verifikator')->name('verifikator.')->group(function () {
    Route::get('/dashboard', [VerifikatorDashboardController::class, 'index'])->name('dashboard');

    Route::get('/berkas/{pengajuan}', [VerifikatorBerkasController::class, 'show'])->name('berkas.show');
    Route::get('/berkas/{pengajuan}/dokumen/{key}', [VerifikatorBerkasController::class, 'lihatDokumen'])->name('berkas.dokumen');
    Route::post('/berkas/{pengajuan}/tolak', [VerifikatorBerkasController::class, 'tolak'])->name('berkas.tolak');
    Route::get('/berkas/{pengajuan}/terima', [VerifikatorBerkasController::class, 'terimaForm'])->name('berkas.terima');
    Route::post('/berkas/{pengajuan}/terima', [VerifikatorBerkasController::class, 'terimaSimpan'])->name('berkas.terima.store');
    Route::get('/berkas/{pengajuan}/cetak', [VerifikatorBerkasController::class, 'cetak'])->name('berkas.cetak');
    Route::get('/berkas/{pengajuan}/cetak/pdf', [VerifikatorBerkasController::class, 'cetakPdf'])->name('berkas.cetak.pdf');
    Route::post('/berkas/{pengajuan}/kirim', [VerifikatorBerkasController::class, 'kirim'])->name('berkas.kirim');

    Route::get('/riwayat', [VerifikatorBerkasController::class, 'riwayat'])->name('riwayat');
});

// Khusus akun verifikator dengan is_admin = true (middleware 'admin', alias di bootstrap/app.php).
Route::middleware(['auth:verifikator', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
    Route::get('/penugasan', [AdminController::class, 'penugasan'])->name('penugasan');
    Route::post('/penugasan/{npsn}', [AdminController::class, 'updatePenugasan'])->name('penugasan.update');
    Route::get('/data-masuk', [AdminController::class, 'dataMasuk'])->name('data-masuk');
});

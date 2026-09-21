<?php

use App\Http\Controllers\Auth\LoginController;

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store']);

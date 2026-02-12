<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::get('/bank-register', [UserController::class, 'createBank'])->name('bank.register');
Route::post('/bank-register', [UserController::class, 'bankRegisterConfirm'])->name('bank.register.submit');

Route::get('/slecic.register', [UserController::class, 'createSlecic'])->name('slecic.register');
Route::post('/slecic.register', [UserController::class, 'slecicRegisterConfirm'])->name('slecic.register.submit');

Route::get('/otp', [AuthController::class, 'verify'])->name('otp.verify');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/slecic-register', [UserController::class, 'slecicRegister']);
    Route::post('/slecic-register', [UserController::class, 'slecicRegisterConfirm']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


}); 
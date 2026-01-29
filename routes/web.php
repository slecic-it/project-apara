<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('login');
});


Route::get('/', function () {
        return view('application');
});

Route::get('/slecic-register', [UserController::class, 'slecicRegister']);
Route::post('/slecic-register-confirm', [UserController::class, 'slecicRegisterConfirm']);

Route::get('/login', [AuthController::class, 'login']);
Route::post('/authenticate', [AuthController::class, 'authenticate']);

Route::post('/dashboard', [AuthController::class, 'dashboard'])->name('login');
Route::post('/authenticate', [AuthController::class, 'authenticate']);

Route::post('/login', [AuthController::class, 'login'])->name('login');


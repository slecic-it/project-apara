<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/slecic-register', [UserController::class, 'slecicRegister']);
Route::post('/slecic-register-confirm', [UserController::class, 'slecicRegisterConfirm']);

Route::get('/login', [AuthController::class, 'login']);
Route::post('/authenticate', [AuthController::class, 'authenticate']);
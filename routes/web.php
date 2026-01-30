<?php

use App\Http\Controllers\ApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;




Route::get('/', function () {
    return view('login');
});


// Route::get('/', function () {
//         return redirect('/application/create');
// });

Route::get('/slecic-register', [UserController::class, 'slecicRegister']);
Route::post('/slecic-register-confirm', [UserController::class, 'slecicRegisterConfirm']);

Route::get('/login', [AuthController::class, 'login']);
Route::post('/authenticate', [AuthController::class, 'authenticate']);


Route::get('/application/create', [ApplicationController::class, 'create']);
Route::get('/get-districts/{provinceId}', [ApplicationController::class, 'getDistricts']);

Route::post('/dashboard', [AuthController::class, 'dashboard'])->name('login');


Route::post('/login', [AuthController::class, 'login'])->name('login');



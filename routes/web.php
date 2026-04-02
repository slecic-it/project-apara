<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/pending', [ApplicationController::class, 'pending'])->name('pending');
Route::get('/accepted', [ApplicationController::class, 'accepted'])->name('accepted');
Route::get('/approved', [ApplicationController::class, 'approved'])->name('approved');
Route::get('/rejected', [ApplicationController::class, 'rejected'])->name('rejected');
Route::get('/all-applications', [ApplicationController::class, 'allApplications'])->name('all-applications');

Route::get('/banks', [ApplicationController::class, 'banks'])->name('banks');
Route::post('/banks', [ApplicationController::class, 'storeBank'])->name('banks.store');
Route::get('/employeelist', [ApplicationController::class, 'employeelist'])->name('employeelist');
Route::get('/reports', [ApplicationController::class, 'reports'])->name('reports');
Route::get('/settings', [ApplicationController::class, 'settings'])->name('settings');
Route::get('/reports/monthly', [ApplicationController::class, 'monthlyReport'])->name('monthly-report');
Route::get('/reports/monthly/pdf', [ApplicationController::class, 'monthlyReportPdf'])->name('monthly-report.pdf');
Route::get('/reports/yearly', [ApplicationController::class, 'yearlyReport'])->name('yearly-report');
Route::get('/reports/performance', [ApplicationController::class, 'performanceReport'])->name('performance-report');
Route::get('/reports/audit-trail', [ApplicationController::class, 'auditTrail'])->name('audit-trail');
Route::get('/support/tickets', [ApplicationController::class, 'tickets'])->name('tickets');
Route::get('/support/faq', [ApplicationController::class, 'faq'])->name('faq');
Route::get('/support/contact', [ApplicationController::class, 'contact'])->name('contact');
Route::get('/invoice', [FinanceController::class, 'invoice'])->name('invoice');
Route::get('/fee-due', [FinanceController::class, 'feeDue'])->name('fee_due');
Route::get('/receipt', [FinanceController::class, 'receipt'])->name('receipt');
Route::get('/history', [FinanceController::class, 'history'])->name('history');


// Route::get('/', function () {
//         return redirect('/application/create');
// });

Route::get('/bank-register', [UserController::class, 'createBank'])->name('bank.register');
Route::post('/bank-register', [UserController::class, 'bankRegisterConfirm'])->name('bank.register.submit');
Route::get('/slecic.register', [UserController::class, 'createSlecic'])->name('slecic.register');
Route::post('/slecic.register', [UserController::class, 'sleRegisterConfirm'])->name('slecic.register.submit');

Route::post('/otp-verify', [HomeController::class, 'verify'])->name('otp.verify');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/bank/dashboard', [ApplicationController::class, 'bankDashboard'])->name('bank.dashboard');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('/slecic-register', [UserController::class, 'slecicRegister']);
    Route::post('/slecic-register', [UserController::class, 'sleRegisterConfirm']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/application/create', [ApplicationController::class, 'create'])->name('application.create');
Route::post('/application/create', [ApplicationController::class, 'store'])->name('application.store');
Route::get('/application/{id}', [ApplicationController::class, 'show'])->name('application.show');
Route::get('/get-districts/{provinceId}', [ApplicationController::class, 'getDistricts']);



}); 

<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\FinanceController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login.form');
Route::get('/bank/login', [AuthController::class, 'showBankLoginForm'])->name('bank.login.form');
Route::get('/bank/{bank}/login', [AuthController::class, 'showBankLoginForm'])->name('bank.login.brand');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/pending', [ApplicationController::class, 'pending'])->middleware('auth')->name('pending');
Route::get('/accepted', [ApplicationController::class, 'accepted'])->middleware('auth')->name('accepted');
Route::get('/approved', [ApplicationController::class, 'approved'])->middleware('auth')->name('approved');
Route::get('/rejected', [ApplicationController::class, 'rejected'])->middleware('auth')->name('rejected');
Route::get('/all-applications', [ApplicationController::class, 'allApplications'])->middleware('auth')->name('all-applications');
Route::get('/notifications', [ApplicationController::class, 'slecicNotifications'])->middleware('auth')->name('notifications');
Route::post('/notifications/{id}/read', [ApplicationController::class, 'markSlecicNotificationRead'])->middleware('auth')->name('notifications.read');
Route::post('/notifications/{id}/unread', [ApplicationController::class, 'markSlecicNotificationUnread'])->middleware('auth')->name('notifications.unread');
Route::post('/application/{id}/marketing-process', [ApplicationController::class, 'updateMarketingProcess'])->middleware('auth')->name('application.marketing.process');

Route::get('/banks', [ApplicationController::class, 'banks'])->middleware('auth')->name('banks');
Route::post('/banks', [ApplicationController::class, 'storeBank'])->middleware('auth')->name('banks.store');
Route::put('/banks/{id}', [ApplicationController::class, 'updateBank'])->middleware('auth')->name('banks.update');
Route::delete('/banks/{id}', [ApplicationController::class, 'destroyBank'])->middleware('auth')->name('banks.destroy');
Route::get('/employeelist', [ApplicationController::class, 'employeelist'])->middleware('auth')->name('employeelist');
Route::get('/reports', [ApplicationController::class, 'reports'])->middleware('auth')->name('reports');
Route::get('/settings', [ApplicationController::class, 'settings'])->middleware('auth')->name('settings');
Route::get('/reports/monthly', [ApplicationController::class, 'monthlyReport'])->middleware('auth')->name('monthly-report');
Route::get('/reports/monthly/pdf', [ApplicationController::class, 'monthlyReportPdf'])->middleware('auth')->name('monthly-report.pdf');
Route::get('/reports/yearly', [ApplicationController::class, 'yearlyReport'])->middleware('auth')->name('yearly-report');
Route::get('/reports/performance', [ApplicationController::class, 'performanceReport'])->middleware('auth')->name('performance-report');
Route::get('/reports/audit-trail', [ApplicationController::class, 'auditTrail'])->middleware('auth')->name('audit-trail');
Route::get('/support/tickets', [ApplicationController::class, 'tickets'])->middleware('auth')->name('tickets');
Route::get('/support/faq', [ApplicationController::class, 'faq'])->middleware('auth')->name('faq');
Route::get('/support/contact', [ApplicationController::class, 'contact'])->middleware('auth')->name('contact');
Route::get('/blacklist', [ApplicationController::class, 'blacklist'])->middleware('auth')->name('blacklist');
Route::get('/invoice', [FinanceController::class, 'invoice'])->middleware('auth')->name('invoice');
Route::get('/fee-due', [FinanceController::class, 'feeDue'])->middleware('auth')->name('fee_due');
Route::get('/receipt', [FinanceController::class, 'receipt'])->middleware('auth')->name('receipt');
Route::get('/history', [FinanceController::class, 'history'])->middleware('auth')->name('history');
Route::get('/payments', [FinanceController::class, 'payments'])->middleware('auth')->name('payments');


// Route::get('/', function () {
//         return redirect('/application/create');
// });

Route::get('/bank-register', [UserController::class, 'createBank'])->middleware('auth')->name('bank.register');
Route::post('/bank-register', [UserController::class, 'bankRegisterConfirm'])->middleware('auth')->name('bank.register.submit');
Route::get('/slecic.register', [UserController::class, 'createSlecic'])->middleware('auth')->name('slecic.register');
Route::post('/slecic.register', [UserController::class, 'sleRegisterConfirm'])->middleware('auth')->name('slecic.register.submit');

Route::post('/otp-verify', [HomeController::class, 'verify'])->name('otp.verify');


Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');
    Route::get('/bank/dashboard', [ApplicationController::class, 'bankDashboard'])->name('bank.dashboard');
    Route::get('/bank/notifications', [ApplicationController::class, 'bankNotifications'])->name('bank.notifications');
    Route::post('/bank/notifications/{id}/read', [ApplicationController::class, 'markBankNotificationRead'])->name('bank.notifications.read');
    Route::post('/bank/notifications/{id}/unread', [ApplicationController::class, 'markBankNotificationUnread'])->name('bank.notifications.unread');
    Route::get('/bank/pending', [ApplicationController::class, 'pending'])->name('bank.pending');
    Route::get('/bank/accepted', [ApplicationController::class, 'accepted'])->name('bank.accepted');
    Route::get('/bank/approved', [ApplicationController::class, 'approved'])->name('bank.approved');
    Route::get('/bank/rejected', [ApplicationController::class, 'rejected'])->name('bank.rejected');
    Route::get('/bank/all-applications', [ApplicationController::class, 'allApplications'])->name('bank.all-applications');
    Route::get('/bank/blacklist', [ApplicationController::class, 'blacklist'])->name('bank.blacklist');
    Route::get('/bank/history', [FinanceController::class, 'history'])->name('bank.history');
    Route::get('/bank/payments', [FinanceController::class, 'payments'])->name('bank.payments');
    Route::get('/bank/reports', [ApplicationController::class, 'reports'])->name('bank.reports');
    Route::get('/bank/reports/monthly', [ApplicationController::class, 'monthlyReport'])->name('bank.monthly-report');
    Route::get('/bank/reports/monthly/pdf', [ApplicationController::class, 'monthlyReportPdf'])->name('bank.monthly-report.pdf');
    Route::get('/bank/reports/yearly', [ApplicationController::class, 'yearlyReport'])->name('bank.yearly-report');
    Route::get('/bank/reports/performance', [ApplicationController::class, 'performanceReport'])->name('bank.performance-report');
    Route::get('/bank/reports/audit-trail', [ApplicationController::class, 'auditTrail'])->name('bank.audit-trail');
    Route::get('/bank/support/tickets', [ApplicationController::class, 'tickets'])->name('bank.tickets');
    Route::get('/bank/support/faq', [ApplicationController::class, 'faq'])->name('bank.faq');
    Route::get('/bank/support/contact', [ApplicationController::class, 'contact'])->name('bank.contact');
    Route::get('/bank/profile', [HomeController::class, 'profile'])->name('bank.profile');
    Route::get('/bank/application/create', [ApplicationController::class, 'create'])->name('bank.application.create');
    Route::post('/bank/application/create', [ApplicationController::class, 'store'])->name('bank.application.store');
    Route::get('/bank/application/{id}', [ApplicationController::class, 'show'])->name('bank.application.show');
    Route::get('/profile', [HomeController::class, 'profile'])->name('profile');
    Route::get('/slecic-register', [UserController::class, 'slecicRegister']);
    Route::post('/slecic-register', [UserController::class, 'sleRegisterConfirm']);
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/application/create', [ApplicationController::class, 'create'])->name('application.create');
Route::post('/application/create', [ApplicationController::class, 'store'])->name('application.store');
Route::get('/application/{id}', [ApplicationController::class, 'show'])->name('application.show');
Route::get('/get-districts/{provinceId}', [ApplicationController::class, 'getDistricts']);



}); 

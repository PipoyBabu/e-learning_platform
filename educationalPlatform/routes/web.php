<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MemberPortalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WeatherController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthController::class, 'create'])->name('login');
    Route::post('login', [AuthController::class, 'store'])->name('login.store');
    Route::get('register', [AuthController::class, 'register'])->name('register');
    Route::post('register', [AuthController::class, 'storeRegistration'])->name('register.store');
});

Route::post('logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/', fn () => redirect()->route(auth()->user()->isAdmin() ? 'dashboard' : 'member.portal'))->name('home');
    Route::get('my-account', [MemberPortalController::class, 'index'])->name('member.portal');
    Route::get('email/verify', [AuthController::class, 'verificationNotice'])->name('verification.notice');
    Route::get('email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [AuthController::class, 'sendVerification'])->middleware('throttle:6,1')->name('verification.send');

    Route::middleware('admin')->group(function (): void {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('categories', CategoryController::class);
        Route::resource('levels', LevelController::class);
        Route::resource('members', MemberController::class)->except('destroy');
        Route::patch('members/{member}/deactivate', [MemberController::class, 'deactivate'])->name('members.deactivate');
        Route::patch('members/{member}/reactivate', [MemberController::class, 'reactivate'])->name('members.reactivate');
        Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
        Route::get('transactions/create/application', [TransactionController::class, 'createApplication'])->name('transactions.create.application');
        Route::post('transactions/application', [TransactionController::class, 'storeApplication'])->name('transactions.store.application');
        Route::get('transactions/create/renewal', [TransactionController::class, 'createRenewal'])->name('transactions.create.renewal');
        Route::post('transactions/renewal', [TransactionController::class, 'storeRenewal'])->name('transactions.store.renewal');
        Route::patch('transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
        Route::patch('transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    });

    Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::get('transactions/{transaction}/receipt', [ReceiptController::class, 'show'])->name('transactions.receipt');
    Route::post('transactions/{transaction}/pay', [PaymentController::class, 'initiate'])->name('transactions.pay');
    Route::get('gateway/checkout/{gatewayReference}', [PaymentController::class, 'checkout'])->name('gateway.checkout');
    Route::post('gateway/callback', [PaymentController::class, 'callback'])->name('gateway.callback');
    Route::get('weather', [WeatherController::class, 'show'])->middleware('throttle:30,1')->name('weather.show');
});

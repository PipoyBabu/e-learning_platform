<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');
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
Route::get('transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
Route::patch('transactions/{transaction}/approve', [TransactionController::class, 'approve'])->name('transactions.approve');
Route::patch('transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
Route::post('transactions/{transaction}/pay', [PaymentController::class, 'initiate'])->name('transactions.pay');
Route::get('gateway/checkout/{gatewayReference}', [PaymentController::class, 'checkout'])->name('gateway.checkout');
Route::post('gateway/callback', [PaymentController::class, 'callback'])->name('gateway.callback');

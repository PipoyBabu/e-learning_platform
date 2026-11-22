<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\MemberController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/members')->name('home');
Route::resource('categories', CategoryController::class);
Route::resource('levels', LevelController::class);
Route::resource('members', MemberController::class)->except('destroy');
Route::patch('members/{member}/deactivate', [MemberController::class, 'deactivate'])->name('members.deactivate');
Route::patch('members/{member}/reactivate', [MemberController::class, 'reactivate'])->name('members.reactivate');

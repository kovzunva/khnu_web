<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MainPageController;

// Реєстрація, автентифікація

Auth::routes(['verify' => true]);

// Клієнтська частина
Route::get('/', [MainPageController::class, 'show'])->name('main');
Route::get('/home', [HomeController::class, 'index'])->name('home');
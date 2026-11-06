<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');


// Authentication Routes
Route::prefix('auth')->name('auth.')->middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::get('/register', 'showRegisterForm')->name('register');
});


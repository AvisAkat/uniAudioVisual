<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Authentication Routes
Route::prefix('auth')->name('auth.')->middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('loginForm');
    Route::get('/register', 'showRegisterForm')->name('registerForm');
    Route::post('/register', 'register')->name('register_handler');
});

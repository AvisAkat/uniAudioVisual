<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Authentication Routes
Route::prefix('auth')->name('auth.')->middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register')->name('register_handler');
    Route::post('/login', 'login')->name('login_handler');
});

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth'])->controller(StudentController::class)->group(function () {
    Route::get('/dashboard', 'showDashboard')->name('dashboard');
});


// Staff Routes
Route::prefix('staff')->name('staff.')->middleware(['auth'])->controller(StaffController::class)->group(function () {
    Route::get('/dashboard', 'showDashboard')->name('dashboard');
});

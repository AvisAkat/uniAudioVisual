<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// Authentication Routes
Route::prefix('auth')->name('auth.')->middleware('guest')->controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLoginForm')->name('login');
    Route::get('/register', 'showRegisterForm')->name('register');
    Route::post('/register', 'register')->name('register_handler');
    Route::post('/login', 'login')->name('login_handler');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

    // Student Routes
    Route::prefix('student')->name('student.')->middleware('student')->controller(StudentController::class)->group(function () {
        Route::get('/dashboard', 'showDashboard')->name('dashboard');
        Route::get('/browse-equipment', 'equipments')->name('browse_equipment');
        Route::get('/check-availability', 'equipmentAvailability')->name('check_availability');
        Route::get('/my-bookings', 'myBookings')->name('my_bookings');
    });

    // Staff Routes
    Route::prefix('staff')->name('staff.')->middleware('staff')->controller(StaffController::class)->group(function () {
        Route::get('/dashboard', 'showDashboard')->name('dashboard');
        Route::get('/booking-request', 'bookingRequest')->name('booking_request');
        Route::get('/activity-log', 'activityLog')->name('activity_log');
        Route::get('/inventory', 'inventory')->name('inventory');
        Route::get('/usage-history', 'usageHistory')->name('usage_history');
        Route::get('/time-slot', 'timeSlot')->name('time_slot');
    });
});

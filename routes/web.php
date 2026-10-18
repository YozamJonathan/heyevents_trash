<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventBookingController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    // Single unified dashboard route - view adapts based on the user's Spatie role
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Event Booking Flow
    Route::get('events/book', [EventBookingController::class, 'create'])->name('events.book');
    Route::post('events/book', [EventBookingController::class, 'store'])->name('events.book.store');
    Route::get('events/my-bookings', [EventBookingController::class, 'myBookings'])->name('events.my-bookings');
});

require __DIR__.'/settings.php';

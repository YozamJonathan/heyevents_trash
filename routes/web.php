<?php

use App\Http\Controllers\Admin\VendorVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventBookingController;
use App\Http\Controllers\Onboarding\ShowOnboardingController;
use App\Http\Controllers\Onboarding\StoreClientRoleController;
use App\Http\Controllers\Onboarding\StoreProviderKycController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
    Route::get('auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Single unified dashboard route - view adapts based on the user's Spatie role
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Onboarding & Role Selection
    Route::get('onboarding', ShowOnboardingController::class)->name('onboarding.index');
    Route::post('onboarding/client', StoreClientRoleController::class)->name('onboarding.client');
    Route::post('onboarding/provider', StoreProviderKycController::class)->name('onboarding.provider');

    // Admin Vendor KYC Verification
    Route::post('admin/vendors/{profile}/approve', [VendorVerificationController::class, 'approve'])->name('admin.vendors.approve');
    Route::post('admin/vendors/{profile}/reject', [VendorVerificationController::class, 'reject'])->name('admin.vendors.reject');

    // Event Booking Flow
    Route::get('events/book', [EventBookingController::class, 'create'])->name('events.book');
    Route::post('events/book', [EventBookingController::class, 'store'])->name('events.book.store');
    Route::get('events/my-bookings', [EventBookingController::class, 'myBookings'])->name('events.my-bookings');
});

require __DIR__.'/settings.php';

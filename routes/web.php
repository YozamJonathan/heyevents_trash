<?php

use App\Http\Controllers\Admin\VendorVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventBookingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Onboarding\ShowOnboardingController;
use App\Http\Controllers\Onboarding\StoreClientRoleController;
use App\Http\Controllers\Onboarding\StoreProviderKycController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/explore', 'Explore')->name('explore');

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

    // Admin Vendor KYC Verification & Messaging
    Route::get('admin/vendor-approvals', [VendorVerificationController::class, 'index'])->name('admin.vendors.index');
    Route::post('admin/vendors/{profile}/approve', [VendorVerificationController::class, 'approve'])->name('admin.vendors.approve');
    Route::post('admin/vendors/{profile}/reject', [VendorVerificationController::class, 'reject'])->name('admin.vendors.reject');
    Route::post('admin/vendors/{profile}/revoke', [VendorVerificationController::class, 'revoke'])->name('admin.vendors.revoke');
    Route::post('admin/vendors/{profile}/message', [VendorVerificationController::class, 'sendMessage'])->name('admin.vendors.message');

    // User Notifications (Client & Vendor)
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Event Booking Flow
    Route::get('events/book', [EventBookingController::class, 'create'])->name('events.book');
    Route::post('events/book', [EventBookingController::class, 'store'])->name('events.book.store');
    Route::get('events/my-bookings', [EventBookingController::class, 'myBookings'])->name('events.my-bookings');
});

require __DIR__.'/settings.php';

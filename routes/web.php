<?php

use App\Http\Controllers\Admin\AdminBookingController;
use App\Http\Controllers\Admin\EventBookingDispatchController;
use App\Http\Controllers\Admin\ItemApprovalController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorVerificationController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventBookingController;
use App\Http\Controllers\ExploreController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Provider\ProviderServiceController;
use App\Http\Controllers\ProviderGigController;
use App\Models\CatalogItem;
use App\Http\Controllers\Onboarding\ShowOnboardingController;
use App\Http\Controllers\Onboarding\StoreClientRoleController;
use App\Http\Controllers\Onboarding\StoreProviderKycController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $featuredItems = CatalogItem::query()
        ->where('is_available', true)
        ->where('status', 'approved')
        ->with(['provider.user'])
        ->latest('is_featured')
        ->latest()
        ->take(24)
        ->get()
        ->map(function (CatalogItem $item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'category' => $item->category,
                'description' => $item->description,
                'price' => $item->formatted_price.' ('.$item->pricing_unit.')',
                'client_price' => (float) $item->client_price,
                'pricing_unit' => $item->pricing_unit,
                'location' => $item->location,
                'image' => $item->image_url ?? 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
                'providerName' => $item->provider?->brand_name ?? 'HEY Events Verified Fleet',
                'stock_quantity' => $item->stock_quantity,
                'is_featured' => (bool) $item->is_featured,
            ];
        });

    return Inertia\Inertia::render('Welcome', [
        'featuredItems' => $featuredItems,
    ]);
})->name('home');
Route::get('/explore', [ExploreController::class, 'index'])->name('explore');

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

    // Admin Vendor KYC Verification & Messaging (Registration Approvals)
    Route::get('admin/vendor-approvals', [VendorVerificationController::class, 'index'])->name('admin.vendors.index');
    Route::post('admin/vendors/{profile}/approve', [VendorVerificationController::class, 'approve'])->name('admin.vendors.approve');
    Route::post('admin/vendors/{profile}/reject', [VendorVerificationController::class, 'reject'])->name('admin.vendors.reject');
    Route::post('admin/vendors/{profile}/revoke', [VendorVerificationController::class, 'revoke'])->name('admin.vendors.revoke');
    Route::post('admin/vendors/{profile}/message', [VendorVerificationController::class, 'sendMessage'])->name('admin.vendors.message');

    // Admin Equipment & Services Upload Approvals (Provider Upload Moderation)
    Route::get('admin/item-approvals', [ItemApprovalController::class, 'index'])->name('admin.items.index');
    Route::post('admin/items/{item}/approve', [ItemApprovalController::class, 'approve'])->name('admin.items.approve');
    Route::post('admin/items/{item}/reject', [ItemApprovalController::class, 'reject'])->name('admin.items.reject');

    // Admin Client Event Bookings & Fulfillment Desk
    Route::get('admin/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');

    // Provider Tools & Equipment Posting
    Route::get('provider/services', [ProviderServiceController::class, 'index'])->name('provider.services.index');
    Route::post('provider/services', [ProviderServiceController::class, 'store'])->name('provider.services.store');
    Route::delete('provider/services/{item}', [ProviderServiceController::class, 'destroy'])->name('provider.services.destroy');

    // User Notifications (Client, Vendor & Admin Real-time Polling)
    Route::get('notifications/latest', [NotificationController::class, 'latest'])->name('notifications.latest');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

    // Admin User Management
    Route::get('admin/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::put('admin/users/{user}/role', [UserController::class, 'updateRole'])->name('admin.users.update-role');
    Route::delete('admin/users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Event Booking Flow
    Route::get('events/book', [EventBookingController::class, 'create'])->name('events.book');
    Route::post('events/book', [EventBookingController::class, 'store'])->name('events.book.store');
    Route::get('events/my-bookings', [EventBookingController::class, 'myBookings'])->name('events.my-bookings');

    // Admin Event Booking Dispatch & Fulfillment Operations
    Route::post('admin/bookings/{booking}/dispatch', [EventBookingDispatchController::class, 'dispatchBooking'])->name('admin.bookings.dispatch');
    Route::post('admin/bookings/{booking}/status', [EventBookingDispatchController::class, 'updateStatus'])->name('admin.bookings.update-status');

    // Provider Gig Fulfillment Actions
    Route::post('provider/bookings/{booking}/progress', [ProviderGigController::class, 'updateProgress'])->name('provider.bookings.progress');
});

require __DIR__.'/settings.php';

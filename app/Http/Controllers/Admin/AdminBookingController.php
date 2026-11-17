<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventBooking;
use App\Models\ServiceProviderProfile;
use Inertia\Inertia;
use Inertia\Response;

class AdminBookingController extends Controller
{
    /**
     * Display the full client event bookings and fulfillment management portal.
     */
    public function index(): Response
    {
        $bookings = EventBooking::with(['user', 'catalogItem', 'assignedProvider'])
            ->latest()
            ->get();

        $verifiedVendors = ServiceProviderProfile::with('user')
            ->where('status', 'approved')
            ->latest()
            ->get();

        return Inertia::render('admin/Bookings', [
            'bookings' => $bookings,
            'verifiedVendors' => $verifiedVendors,
        ]);
    }
}

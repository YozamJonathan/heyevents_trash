<?php

namespace App\Http\Controllers;

use App\Models\ServiceProviderProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Render the unified dashboard page.
     * The frontend dynamically renders the appropriate UI based on the user's Spatie role.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        // If user has no roles yet, ensure they are given default Client role so they never get stuck
        if ($user && $user->roles()->count() === 0) {
            Role::findOrCreate('Client', 'web');
            $user->assignRole('Client');
        }

        $bookedEvents = $user ? $user->eventBookings()->latest()->get() : [];

        $canVerify = $user && ($user->can('verify service providers') || $user->hasRole('Super Admin'));

        $pendingVendors = $canVerify
            ? ServiceProviderProfile::with(['user', 'feedbackSender'])->where('status', 'pending')->latest()->get()
            : [];

        $verifiedVendors = $canVerify
            ? ServiceProviderProfile::with(['user', 'verifier', 'feedbackSender'])->where('status', 'approved')->latest()->get()
            : [];

        $rejectedVendors = $canVerify
            ? ServiceProviderProfile::with(['user', 'verifier', 'feedbackSender'])->where('status', 'rejected')->latest()->get()
            : [];

        $providerProfile = $user ? $user->serviceProviderProfile : null;
        if ($providerProfile) {
            $providerProfile->load(['verifier', 'feedbackSender']);
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'users_count' => User::count(),
                'roles_count' => Role::count(),
                'permissions_count' => Permission::count(),
                'pending_kyc_count' => ServiceProviderProfile::where('status', 'pending')->count(),
                'verified_vendors_count' => ServiceProviderProfile::where('status', 'approved')->count(),
            ],
            'booked_events' => $bookedEvents,
            'pending_vendors' => $pendingVendors,
            'verified_vendors' => $verifiedVendors,
            'rejected_vendors' => $rejectedVendors,
            'provider_profile' => $providerProfile,
        ]);
    }
}

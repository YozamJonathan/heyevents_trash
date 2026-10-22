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

        // If user has no roles yet, redirect them to select their role and complete onboarding
        if ($user && $user->roles()->count() === 0) {
            return redirect()->route('onboarding.index');
        }

        $bookedEvents = $user ? $user->eventBookings()->latest()->get() : [];

        $pendingVendors = ($user && ($user->can('verify service providers') || $user->hasRole('Super Admin')))
            ? ServiceProviderProfile::with('user')->where('status', 'pending')->latest()->get()
            : [];

        $providerProfile = $user ? $user->serviceProviderProfile : null;

        return Inertia::render('Dashboard', [
            'stats' => [
                'users_count' => User::count(),
                'roles_count' => Role::count(),
                'permissions_count' => Permission::count(),
                'pending_kyc_count' => ServiceProviderProfile::where('status', 'pending')->count(),
            ],
            'booked_events' => $bookedEvents,
            'pending_vendors' => $pendingVendors,
            'provider_profile' => $providerProfile,
        ]);
    }
}

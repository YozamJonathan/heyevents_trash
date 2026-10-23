<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceProviderProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VendorVerificationController extends Controller
{
    /**
     * Display dedicated Vendor KYC Approvals listing.
     */
    public function index(Request $request): Response
    {
        if (! $request->user()->can('verify service providers') && ! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $status = $request->query('status', 'all');

        $query = ServiceProviderProfile::with(['user', 'verifier'])->latest();

        if (in_array($status, ['pending', 'approved', 'rejected'], true)) {
            $query->where('status', $status);
        }

        $vendors = $query->get();

        $counts = [
            'all' => ServiceProviderProfile::count(),
            'pending' => ServiceProviderProfile::where('status', 'pending')->count(),
            'approved' => ServiceProviderProfile::where('status', 'approved')->count(),
            'rejected' => ServiceProviderProfile::where('status', 'rejected')->count(),
        ];

        return Inertia::render('admin/VendorApprovals', [
            'vendors' => $vendors,
            'currentStatus' => $status,
            'counts' => $counts,
        ]);
    }

    /**
     * Approve a vendor KYC profile.
     */
    public function approve(Request $request, ServiceProviderProfile $profile): RedirectResponse
    {
        if (! $request->user()->can('verify service providers') && ! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $profile->update([
            'status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
            'rejection_reason' => null,
        ]);

        return back()->with('success', "Vendor \"{$profile->brand_name}\" has been successfully verified.");
    }

    /**
     * Reject a vendor KYC profile.
     */
    public function reject(Request $request, ServiceProviderProfile $profile): RedirectResponse
    {
        if (! $request->user()->can('verify service providers') && ! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $profile->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'] ?? 'Provided documentation could not be verified.',
        ]);

        return back()->with('success', "Vendor \"{$profile->brand_name}\" KYC application rejected.");
    }
}

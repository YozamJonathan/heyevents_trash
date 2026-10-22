<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceProviderProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VendorVerificationController extends Controller
{
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

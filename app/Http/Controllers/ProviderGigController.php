<?php

namespace App\Http\Controllers;

use App\Models\EventBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProviderGigController extends Controller
{
    /**
     * Update progress of an assigned gig by the service provider.
     */
    public function updateProgress(Request $request, EventBooking $booking): RedirectResponse
    {
        $providerProfile = $request->user()->serviceProviderProfile;

        if (!$providerProfile || $booking->assigned_provider_id !== $providerProfile->id) {
            abort(403, 'Unauthorized. This gig is not assigned to your profile.');
        }

        $validated = $request->validate([
            'dispatch_status' => ['required', 'string', 'in:in_transit,delivered_and_setup'],
        ]);

        $booking->update([
            'dispatch_status' => $validated['dispatch_status'],
            'status' => $validated['dispatch_status'] === 'delivered_and_setup' ? 'Delivered & Set Up' : 'Gear In Transit',
        ]);

        $statusLabel = $validated['dispatch_status'] === 'delivered_and_setup'
            ? 'Delivered & Set Up at Venue'
            : 'En Route / In Transit';

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Gig status updated: '.$statusLabel.'. HEY Events coordination desk notified.',
        ]);

        return back();
    }
}

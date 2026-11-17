<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventBooking;
use App\Models\ServiceProviderProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventBookingDispatchController extends Controller
{
    /**
     * Dispatch an event booking to a verified service provider.
     */
    public function dispatchBooking(Request $request, EventBooking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'provider_id' => ['required', 'exists:service_provider_profiles,id'],
            'provider_payout_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $provider = ServiceProviderProfile::with('user')->findOrFail($validated['provider_id']);

        $booking->update([
            'assigned_provider_id' => $provider->id,
            'provider_payout_amount' => $validated['provider_payout_amount'] ?? $booking->provider_payout_amount,
            'dispatch_status' => 'assigned',
            'status' => 'Confirmed & Dispatched',
        ]);

        // Send in-app notification to the assigned provider
        if ($provider->user) {
            DB::table('notifications')->insert([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\Notifications\NewGigDispatched',
                'notifiable_type' => User::class,
                'notifiable_id' => $provider->user->id,
                'data' => json_encode([
                    'title' => 'New Gig Dispatched: '.$booking->name,
                    'message' => 'HEY Events has assigned you to deliver gear for "'.$booking->name.'" at '.($booking->venue_name ?? $booking->location ?? 'Tanzania').'. Agreed Payout: TZS '.number_format((float) ($booking->provider_payout_amount ?? 0)),
                    'booking_id' => $booking->id,
                    'action_url' => '/dashboard',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Gig successfully dispatched to '.$provider->brand_name.'.',
        ]);

        return back();
    }

    /**
     * Update dispatch status or release payout for an event booking.
     */
    public function updateStatus(Request $request, EventBooking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'dispatch_status' => ['nullable', 'string', 'in:pending_dispatch,assigned,in_transit,delivered_and_setup,completed,cancelled'],
            'payout_status' => ['nullable', 'string', 'in:unpaid,pending_payout,paid'],
            'status' => ['nullable', 'string', 'max:100'],
        ]);

        $updates = [];
        if (!empty($validated['dispatch_status'])) {
            $updates['dispatch_status'] = $validated['dispatch_status'];
            if ($validated['dispatch_status'] === 'completed') {
                $updates['status'] = 'Completed';
                $updates['payout_status'] = 'pending_payout';
            } elseif ($validated['dispatch_status'] === 'delivered_and_setup') {
                $updates['status'] = 'Delivered & Set Up';
            }
        }

        if (!empty($validated['payout_status'])) {
            $updates['payout_status'] = $validated['payout_status'];
        }

        if (!empty($validated['status'])) {
            $updates['status'] = $validated['status'];
        }

        $booking->update($updates);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Booking status updated successfully.',
        ]);

        return back();
    }
}

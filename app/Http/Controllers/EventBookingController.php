<?php

namespace App\Http\Controllers;

use App\Models\EventBooking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventBookingController extends Controller
{
    /**
     * Show the form for booking a new event.
     */
    public function create(): Response
    {
        return Inertia::render('events/Book');
    }

    /**
     * Handle the booking submission.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'event_type' => ['required', 'string', 'max:100'],
            'services' => ['required', 'array', 'min:1'],
            'services.*' => ['string'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'event_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $request->user()->eventBookings()->create([
            'name' => $validated['name'],
            'event_type' => $validated['event_type'],
            'services' => $validated['services'],
            'budget' => $validated['budget'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'location' => $validated['location'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'Pending Review',
        ]);

        $notificationMessage = 'Thanks! The team will reach you soon.';

        session()->flash('booking_success', $notificationMessage);
        session()->flash('success', $notificationMessage);
        session()->flash('toast', [
            'type' => 'success',
            'message' => $notificationMessage,
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Display the client's booked events.
     */
    public function myBookings(Request $request): Response
    {
        $bookings = $request->user()
            ->eventBookings()
            ->latest()
            ->get();

        return Inertia::render('events/MyBookings', [
            'bookings' => $bookings,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use App\Models\EventBooking;
use App\Models\User;
use App\Notifications\ClientBookingNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventBookingController extends Controller
{
    /**
     * Show the form for booking a new event or catalog item.
     */
    public function create(Request $request): Response
    {
        $selectedItem = null;
        $selectedItems = collect();

        if ($itemIds = $request->query('items')) {
            $ids = is_array($itemIds) ? $itemIds : explode(',', (string) $itemIds);
            $selectedItems = CatalogItem::with('provider')
                ->whereIn('id', $ids)
                ->where('status', 'approved')
                ->get();
            $selectedItem = $selectedItems->first();
        } elseif ($itemId = $request->query('item')) {
            $selectedItem = CatalogItem::with('provider')->find($itemId);
            if ($selectedItem) {
                $selectedItems = collect([$selectedItem]);
            }
        }

        return Inertia::render('events/Book', [
            'preselectedItem' => $selectedItem,
            'preselectedItems' => $selectedItems,
        ]);
    }

    /**
     * Handle the booking submission.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client_phone' => ['nullable', 'string', 'max:50'],
            'event_type' => ['required', 'string', 'max:100'],
            'services' => ['required_without_all:order_items,catalog_item_id', 'nullable', 'array'],
            'services.*' => ['string'],
            'order_items' => ['nullable', 'array'],
            'order_items.*.id' => ['required_with:order_items', 'integer'],
            'order_items.*.quantity' => ['required_with:order_items', 'integer', 'min:1'],
            'catalog_item_id' => ['nullable', 'exists:catalog_items,id'],
            'item_quantity' => ['nullable', 'integer', 'min:1'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'event_date' => ['nullable', 'date'],
            'location' => ['nullable', 'string', 'max:255'],
            'venue_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $orderItemsPayload = [];
        $totalClientAmount = null;
        $providerPayoutAmount = null;
        $assignedProviderId = null;
        $catalogItemId = null;
        $itemQuantity = 1;

        if (!empty($validated['order_items']) && count($validated['order_items']) > 0) {
            $itemMap = collect($validated['order_items'])->keyBy('id');
            $dbItems = CatalogItem::whereIn('id', $itemMap->keys())->get();

            $totalClientAmount = 0;
            $providerPayoutAmount = 0;
            $distinctProviders = [];

            foreach ($dbItems as $dbItem) {
                $qty = (int) ($itemMap[$dbItem->id]['quantity'] ?? 1);
                $subtotal = (float) $dbItem->client_price * $qty;
                $payoutSubtotal = (float) $dbItem->provider_payout * $qty;

                $totalClientAmount += $subtotal;
                $providerPayoutAmount += $payoutSubtotal;

                if ($dbItem->service_provider_profile_id) {
                    $distinctProviders[] = $dbItem->service_provider_profile_id;
                }

                $orderItemsPayload[] = [
                    'id' => $dbItem->id,
                    'title' => $dbItem->title,
                    'category' => $dbItem->category,
                    'quantity' => $qty,
                    'client_price' => (float) $dbItem->client_price,
                    'pricing_unit' => $dbItem->pricing_unit,
                    'subtotal' => $subtotal,
                    'provider_payout' => (float) $dbItem->provider_payout,
                    'image_url' => $dbItem->image_url,
                    'provider_id' => $dbItem->service_provider_profile_id,
                ];
            }

            if (count($dbItems) === 1) {
                $first = $dbItems->first();
                $catalogItemId = $first->id;
                $itemQuantity = (int) ($itemMap[$first->id]['quantity'] ?? 1);
            }

            $distinctProviders = array_unique($distinctProviders);
            if (count($distinctProviders) === 1) {
                $assignedProviderId = reset($distinctProviders);
            }
        } elseif (!empty($validated['catalog_item_id'])) {
            $catalogItem = CatalogItem::find($validated['catalog_item_id']);
            if ($catalogItem) {
                $catalogItemId = $catalogItem->id;
                $itemQuantity = (int) ($validated['item_quantity'] ?? 1);
                $totalClientAmount = (float) $catalogItem->client_price * $itemQuantity;
                $providerPayoutAmount = (float) $catalogItem->provider_payout * $itemQuantity;
                $assignedProviderId = $catalogItem->service_provider_profile_id;

                $orderItemsPayload[] = [
                    'id' => $catalogItem->id,
                    'title' => $catalogItem->title,
                    'category' => $catalogItem->category,
                    'quantity' => $itemQuantity,
                    'client_price' => (float) $catalogItem->client_price,
                    'pricing_unit' => $catalogItem->pricing_unit,
                    'subtotal' => $totalClientAmount,
                    'provider_payout' => (float) $catalogItem->provider_payout,
                    'image_url' => $catalogItem->image_url,
                    'provider_id' => $catalogItem->service_provider_profile_id,
                ];
            }
        } else {
            $totalClientAmount = $validated['budget'] ?? null;
        }

        $services = $validated['services'] ?? [];
        if (empty($services) && !empty($orderItemsPayload)) {
            $services = collect($orderItemsPayload)->pluck('title')->all();
        }

        $booking = $request->user()->eventBookings()->create([
            'catalog_item_id' => $catalogItemId,
            'assigned_provider_id' => $assignedProviderId,
            'item_quantity' => $itemQuantity,
            'order_items' => !empty($orderItemsPayload) ? $orderItemsPayload : null,
            'name' => $validated['name'],
            'client_phone' => $validated['client_phone'] ?? null,
            'event_type' => $validated['event_type'],
            'services' => $services,
            'budget' => $validated['budget'] ?? null,
            'total_client_amount' => $totalClientAmount,
            'provider_payout_amount' => $providerPayoutAmount,
            'event_date' => $validated['event_date'] ?? null,
            'location' => $validated['location'] ?? null,
            'venue_name' => $validated['venue_name'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'Pending Review',
            'dispatch_status' => $assignedProviderId ? 'assigned' : 'pending_dispatch',
            'payout_status' => 'unpaid',
        ]);

        // Notify Admins about new client booking
        $admins = User::role(['Admin', 'Super Admin'])->get();
        $targetDesc = !empty($orderItemsPayload)
            ? count($orderItemsPayload).' item(s): '.collect($orderItemsPayload)->pluck('title')->take(2)->join(', ')
            : ($validated['event_type'] ?? 'Custom Event');
        foreach ($admins as $admin) {
            $admin->notify(new ClientBookingNotification(
                title: 'New Client Event Booking',
                message: "Client \"{$request->user()->name}\" requested \"{$validated['name']}\" ({$targetDesc}). Location: ".($validated['venue_name'] ?? $validated['location'] ?? 'Tanzania').".",
                type: 'client_booking',
                actionUrl: '/admin/bookings',
                senderName: $request->user()->name
            ));
        }

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
            ->with(['catalogItem', 'assignedProvider'])
            ->latest()
            ->get();

        return Inertia::render('events/MyBookings', [
            'bookings' => $bookings,
        ]);
    }
}

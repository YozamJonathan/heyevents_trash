<?php

namespace App\Http\Controllers;

use App\Models\CatalogItem;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExploreController extends Controller
{
    /**
     * Display the equipment, services, and venues catalog.
     */
    public function index(Request $request): Response
    {
        $items = CatalogItem::query()
            ->where('is_available', true)
            ->where('status', 'approved')
            ->with(['provider.user'])
            ->latest('is_featured')
            ->latest()
            ->get()
            ->map(function (CatalogItem $item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'category' => $item->category,
                    'description' => $item->description,
                    'price' => $item->formatted_price.' ('.$item->pricing_unit.')',
                    'client_price' => (float) $item->client_price,
                    'provider_payout' => (float) $item->provider_payout,
                    'pricing_unit' => $item->pricing_unit,
                    'rating' => 4.9,
                    'reviewsCount' => 18,
                    'location' => $item->location,
                    'image' => $item->image_url ?? 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
                    'providerName' => $item->provider?->brand_name ?? 'HEY Events Verified Fleet',
                    'providerId' => $item->provider?->id,
                    'verified' => true,
                    'badge' => $item->is_featured ? 'HEY Events Verified' : null,
                    'stock_quantity' => $item->stock_quantity,
                ];
            });

        return Inertia::render('Explore', [
            'catalogItems' => $items,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\CatalogItem;
use App\Models\User;
use App\Notifications\ItemUploadNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProviderServiceController extends Controller
{
    /**
     * Display the provider's posted tools, equipment, and services.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $profile = $request->user()->serviceProviderProfile;

        if (!$profile) {
            return redirect()->route('onboarding.index');
        }

        $items = $profile->catalogItems()->latest()->get();

        return Inertia::render('provider/Services', [
            'items' => $items,
            'isApproved' => $profile->isApproved(),
        ]);
    }

    /**
     * Store a new equipment or service item posted by the provider.
     * All items start in "pending" status until approved by HEY Events Admin.
     */
    public function store(Request $request): RedirectResponse
    {
        $profile = $request->user()->serviceProviderProfile;

        if (!$profile || !$profile->isApproved()) {
            abort(403, 'Your account must be KYC-approved before posting tools and services.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
            'provider_payout' => ['required', 'numeric', 'min:500'],
            'pricing_unit' => ['required', 'string', 'max:50'],
            'stock_quantity' => ['required', 'integer', 'min:1'],
            'location' => ['required', 'string', 'max:255'],
            'image_url' => ['nullable', 'string', 'max:1000'],
        ]);

        $payout = (float) $validated['provider_payout'];
        // Standard platform listing markup so client pays fair market price
        $clientPrice = round($payout * 1.2, -2);

        $item = $profile->catalogItems()->create([
            'title' => $validated['title'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'provider_payout' => $payout,
            'client_price' => $clientPrice,
            'pricing_unit' => $validated['pricing_unit'],
            'stock_quantity' => $validated['stock_quantity'],
            'location' => $validated['location'],
            'image_url' => $validated['image_url'] ?? 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
            'status' => 'pending', // Requires admin review
            'is_available' => false, // Hidden until verified
            'is_featured' => false,
        ]);

        // Notify Admins about new equipment/service awaiting verification
        $admins = User::role(['Admin', 'Super Admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new ItemUploadNotification(
                title: 'New Equipment / Tool Upload',
                message: "Provider \"{$profile->brand_name}\" uploaded \"{$validated['title']}\" (TZS ".number_format($payout)."). Awaiting your verification.",
                type: 'pending_item',
                actionUrl: '/admin/item-approvals',
                senderName: $profile->brand_name
            ));
        }

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Tool / Service uploaded! It will be reviewed by HEY Events Admin before appearing in the public Explore catalog.',
        ]);

        return back();
    }

    /**
     * Delete an item.
     */
    public function destroy(Request $request, CatalogItem $item): RedirectResponse
    {
        $profile = $request->user()->serviceProviderProfile;

        if (!$profile || $item->service_provider_profile_id !== $profile->id) {
            abort(403, 'Unauthorized.');
        }

        $item->delete();

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Item removed successfully.',
        ]);

        return back();
    }
}

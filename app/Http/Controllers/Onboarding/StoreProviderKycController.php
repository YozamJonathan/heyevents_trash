<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\StoreProviderKycRequest;
use App\Models\User;
use App\Notifications\VendorKycNotification;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

class StoreProviderKycController extends Controller
{
    /**
     * Submit Service Provider verification details (KYC) and assign role.
     */
    public function __invoke(StoreProviderKycRequest $request): RedirectResponse
    {
        $user = $request->user();
        $existingProfile = $user->serviceProviderProfile;
        $validated = $request->validated();

        // Store business licence file securely if uploaded
        $licencePath = $request->hasFile('business_licence')
            ? $request->file('business_licence')->store('vendor_licences', 'public')
            : ($existingProfile?->business_licence_path ?? '');

        // Create or update provider profile
        $user->serviceProviderProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'brand_name' => $validated['brand_name'],
                'contact_name' => $validated['contact_name'],
                'nida_number' => (string) $validated['nida_number'],
                'primary_phone' => (string) $validated['primary_phone'],
                'secondary_phone' => isset($validated['secondary_phone']) ? (string) $validated['secondary_phone'] : null,
                'business_licence_path' => $licencePath,
                'categories' => $validated['categories'],
                'category' => $validated['categories'][0] ?? null,
                'instagram' => $validated['instagram'] ?? null,
                'whatsapp' => $validated['whatsapp'] ?? null,
                'tiktok' => $validated['tiktok'] ?? null,
                'social_media' => $validated['social_media'] ?? null,
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
                'rejection_reason' => null,
            ]
        );

        Role::findOrCreate('Service Provider', 'web');

        if (! $user->hasRole('Service Provider')) {
            $user->assignRole('Service Provider');
        }

        // Notify platform administrators about new vendor KYC submission
        $admins = User::role(['Admin', 'Super Admin'])->get();
        foreach ($admins as $admin) {
            $admin->notify(new VendorKycNotification(
                title: 'New Vendor KYC Application',
                message: "New provider \"{$validated['brand_name']}\" ({$user->name}) submitted onboarding documents awaiting your verification.",
                type: 'pending',
                actionUrl: '/admin/vendor-approvals',
                senderName: $user->name
            ));
        }

        if ($request->hasSession()) {
            $request->session()->forget('url.intended');
        }

        return redirect()->route('dashboard')->with(
            'success',
            __('onboarding.providerSubmitted') ?? 'Verification submitted! Your KYC documents are under review by our administration team.'
        );
    }
}

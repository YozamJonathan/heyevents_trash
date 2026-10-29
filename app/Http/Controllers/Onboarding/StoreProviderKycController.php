<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use App\Http\Requests\Onboarding\StoreProviderKycRequest;
use Illuminate\Http\RedirectResponse;

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

        $socialSummary = collect([
            ! empty($validated['instagram']) ? 'IG: '.$validated['instagram'] : null,
            ! empty($validated['whatsapp']) ? 'WA: '.$validated['whatsapp'] : null,
            ! empty($validated['tiktok']) ? 'TT: '.$validated['tiktok'] : null,
        ])->filter()->implode(' • ');

        // Create or update provider profile
        $user->serviceProviderProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'brand_name' => $validated['brand_name'],
                'contact_name' => $validated['contact_name'],
                'nida_number' => $validated['nida_number'],
                'primary_phone' => $validated['primary_phone'],
                'secondary_phone' => $validated['secondary_phone'] ?? null,
                'business_licence_path' => $licencePath,
                'categories' => $validated['categories'],
                'category' => $validated['categories'][0] ?? null,
                'instagram' => $validated['instagram'] ?? null,
                'whatsapp' => $validated['whatsapp'] ?? null,
                'tiktok' => $validated['tiktok'] ?? null,
                'social_media' => $socialSummary ?: 'Provided in application',
                'description' => $validated['description'] ?? null,
                'status' => 'pending',
                'rejection_reason' => null,
            ]
        );

        if (! $user->hasRole('Service Provider')) {
            $user->assignRole('Service Provider');
        }

        return redirect()->route('dashboard')->with(
            'success',
            __('onboarding.providerSubmitted') ?? 'Verification submitted! Your KYC documents are under review by our administration team.'
        );
    }
}

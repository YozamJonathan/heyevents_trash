<?php

namespace App\Http\Controllers;

use App\Models\ServiceProviderProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    /**
     * Display the role selection and onboarding screen.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $profile = $user ? $user->serviceProviderProfile : null;

        // If user already has a role and isn't a rejected vendor seeking to re-submit, redirect to dashboard
        if ($user && $user->roles()->count() > 0 && (! $profile || $profile->status !== 'rejected')) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('onboarding/SelectRole', [
            'existingProfile' => $profile,
        ]);
    }

    /**
     * Assign the Client role and proceed straight to dashboard.
     */
    public function selectClient(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->roles()->count() === 0) {
            $user->assignRole('Client');
        }

        return redirect()->route('dashboard');
    }

    /**
     * Submit Service Provider verification details (KYC) and assign role.
     */
    public function submitProvider(Request $request): RedirectResponse
    {
        $user = $request->user();
        $existingProfile = $user->serviceProviderProfile;

        $licenceRule = ($existingProfile && $existingProfile->business_licence_path)
            ? ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120']
            : ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'];

        $validated = $request->validate([
            'brand_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'nida_number' => ['required', 'string', 'max:50'],
            'primary_phone' => ['required', 'string', 'max:50'],
            'secondary_phone' => ['nullable', 'string', 'max:50'],
            'business_licence' => $licenceRule,
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['string', 'max:100'],
            'instagram' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'tiktok' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

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

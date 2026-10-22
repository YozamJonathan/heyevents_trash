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

        $validated = $request->validate([
            'brand_name' => ['required', 'string', 'max:255'],
            'contact_name' => ['required', 'string', 'max:255'],
            'nida_number' => ['required', 'string', 'max:50'],
            'business_licence' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'social_media' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        // Store business licence file securely
        $licencePath = $request->file('business_licence')->store('vendor_licences', 'public');

        // Create or update provider profile
        $user->serviceProviderProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'brand_name' => $validated['brand_name'],
                'contact_name' => $validated['contact_name'],
                'nida_number' => $validated['nida_number'],
                'business_licence_path' => $licencePath,
                'social_media' => $validated['social_media'],
                'category' => $validated['category'] ?? null,
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

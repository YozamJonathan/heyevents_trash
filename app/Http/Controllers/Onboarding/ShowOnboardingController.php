<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ShowOnboardingController extends Controller
{
    /**
     * Display the role selection and onboarding screen.
     */
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $profile = $user ? $user->serviceProviderProfile : null;

        // If user already has a role and has NO provider profile and didn't ask to create one,
        // pure clients can go to dashboard. But if user has an existing provider profile
        // or has Service Provider role, ALWAYS let them access onboarding to edit/rectify KYC details.
        if ($user && $user->roles()->count() > 0 && ! $profile && ! $user->hasRole('Service Provider') && ! $request->has('role')) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('onboarding/SelectRole', [
            'existingProfile' => $profile,
        ]);
    }
}

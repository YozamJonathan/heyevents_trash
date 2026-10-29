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

        // If user already has a role and isn't a rejected vendor seeking to re-submit, redirect to dashboard
        if ($user && $user->roles()->count() > 0 && (! $profile || $profile->status !== 'rejected')) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('onboarding/SelectRole', [
            'existingProfile' => $profile,
        ]);
    }
}

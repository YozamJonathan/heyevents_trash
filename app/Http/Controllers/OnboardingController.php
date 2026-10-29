<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Onboarding\ShowOnboardingController;
use App\Http\Controllers\Onboarding\StoreClientRoleController;
use App\Http\Controllers\Onboarding\StoreProviderKycController;
use App\Http\Requests\Onboarding\StoreProviderKycRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class OnboardingController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        return app(ShowOnboardingController::class)($request);
    }

    public function selectClient(Request $request): RedirectResponse
    {
        return app(StoreClientRoleController::class)($request);
    }

    public function submitProvider(Request $request): RedirectResponse
    {
        $storeRequest = app(StoreProviderKycRequest::class);

        return app(StoreProviderKycController::class)($storeRequest);
    }
}

<?php

namespace App\Http\Controllers\Onboarding;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class StoreClientRoleController extends Controller
{
    /**
     * Assign the Client role and proceed straight to dashboard.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user && $user->roles()->count() === 0) {
            Role::findOrCreate('Client', 'web');
            $user->assignRole('Client');
        }

        return redirect()->route('dashboard');
    }
}

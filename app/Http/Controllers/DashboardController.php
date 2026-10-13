<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    /**
     * Render the unified dashboard page.
     * The frontend dynamically renders the appropriate UI based on the user's Spatie role.
     */
    public function index(Request $request): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'users_count' => User::count(),
                'roles_count' => Role::count(),
                'permissions_count' => Permission::count(),
            ],
        ]);
    }
}

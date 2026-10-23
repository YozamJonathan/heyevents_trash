<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of all users for administrative management.
     */
    public function index(Request $request): Response
    {
        $currentUser = $request->user();

        if (! $currentUser->can('view users') && ! $currentUser->hasRole('Super Admin')) {
            abort(403);
        }

        $search = $request->query('search');
        $roleFilter = $request->query('role');

        $query = User::with(['roles', 'serviceProviderProfile'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter && $roleFilter !== 'all') {
            $query->whereHas('roles', function ($q) use ($roleFilter) {
                $q->where('name', $roleFilter);
            });
        }

        $users = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => User::count(),
            'admins' => User::role(['Super Admin', 'Admin'])->count(),
            'providers' => User::role('Service Provider')->count(),
            'clients' => User::role('Client')->count(),
        ];

        $availableRoles = Role::pluck('name')->values()->all();

        return Inertia::render('admin/Users', [
            'users' => $users,
            'stats' => $stats,
            'filters' => [
                'search' => $search ?? '',
                'role' => $roleFilter ?? 'all',
            ],
            'availableRoles' => $availableRoles,
            'canEditUsers' => $currentUser->can('edit users') || $currentUser->hasRole('Super Admin'),
            'canDeleteUsers' => $currentUser->can('delete users') || $currentUser->hasRole('Super Admin'),
        ]);
    }

    /**
     * Update the assigned role for a user.
     */
    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->can('edit users') && ! $currentUser->hasRole('Super Admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Only Super Admins can assign or revoke the Super Admin role
        if ($validated['role'] === 'Super Admin' && ! $currentUser->hasRole('Super Admin')) {
            abort(403, 'Only Super Admins can assign the Super Admin role.');
        }

        if ($user->hasRole('Super Admin') && ! $currentUser->hasRole('Super Admin')) {
            abort(403, 'Only Super Admins can modify other Super Admins.');
        }

        $user->syncRoles([$validated['role']]);

        return back()->with('success', "Role for \"{$user->name}\" was updated to {$validated['role']}.");
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->can('delete users') && ! $currentUser->hasRole('Super Admin')) {
            abort(403);
        }

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->email === 'superadmin@heyevents.com') {
            return back()->with('error', 'The primary platform Super Admin account cannot be deleted.');
        }

        $userName = $user->name;
        $user->delete();

        return back()->with('success', "User account \"{$userName}\" was successfully deleted.");
    }
}

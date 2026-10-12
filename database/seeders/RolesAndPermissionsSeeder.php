<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define distinct permissions grouped by capability
        $permissions = [
            // Super Admin level permissions (System & Governance)
            'manage platform settings',
            'manage roles and permissions',
            'view audit logs',
            'manage commission rates',
            'manage database backups',
            'manage admin accounts',

            // Admin level permissions (Operational & Moderation)
            'view users',
            'edit users',
            'delete users',
            'verify service providers',
            'moderate events',
            'manage categories',
            'view all bookings',
            'resolve disputes',
            'view platform analytics',
            'export reports',

            // Service Provider permissions
            'create services',
            'edit services',
            'delete services',
            'manage service packages',
            'view provider bookings',
            'accept reject bookings',
            'manage availability',
            'view provider earnings',
            'request payout',
            'manage provider profile',

            // Client permissions
            'browse services',
            'create bookings',
            'view my bookings',
            'cancel my bookings',
            'submit reviews',
            'make payments',
            'view client receipts',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // 2. Create Roles
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $providerRole = Role::firstOrCreate(['name' => 'Service Provider', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'Client', 'guard_name' => 'web']);

        // 3. Assign Permissions to Roles
        // Super Admin gets everything
        $superAdminRole->syncPermissions(Permission::all());

        // Admin gets platform management & moderation permissions
        $adminRole->syncPermissions([
            'view users',
            'edit users',
            'verify service providers',
            'moderate events',
            'manage categories',
            'view all bookings',
            'resolve disputes',
            'view platform analytics',
            'export reports',
        ]);

        // Service Provider gets vendor & listing management permissions
        $providerRole->syncPermissions([
            'create services',
            'edit services',
            'delete services',
            'manage service packages',
            'view provider bookings',
            'accept reject bookings',
            'manage availability',
            'view provider earnings',
            'request payout',
            'manage provider profile',
        ]);

        // Client gets discovery, booking & payment permissions
        $clientRole->syncPermissions([
            'browse services',
            'create bookings',
            'view my bookings',
            'cancel my bookings',
            'submit reviews',
            'make payments',
            'view client receipts',
        ]);

        // 4. Create default demo users for each role with password: 'password'
        $users = [
            [
                'name' => 'Sarah Connor (Super Admin)',
                'email' => 'superadmin@heyevents.com',
                'role' => $superAdminRole,
            ],
            [
                'name' => 'Marcus Vance (Admin)',
                'email' => 'admin@heyevents.com',
                'role' => $adminRole,
            ],
            [
                'name' => 'Elena Rostova (Photography & DJ Pro)',
                'email' => 'provider@heyevents.com',
                'role' => $providerRole,
            ],
            [
                'name' => 'David Kim (Event Organizer & Client)',
                'email' => 'client@heyevents.com',
                'role' => $clientRole,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$userData['role']]);
        }
    }
}

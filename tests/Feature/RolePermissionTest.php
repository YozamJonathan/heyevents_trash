<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

test('users can have roles and permissions assigned', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'Event Manager']);
    $permission = Permission::create(['name' => 'manage events']);

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    expect($user->hasRole('Event Manager'))->toBeTrue();
    expect($user->hasPermissionTo('manage events'))->toBeTrue();
});

test('super admin bypasses all permission gates', function () {
    $superAdmin = User::factory()->create();
    $superAdminRole = Role::create(['name' => 'Super Admin']);
    $superAdmin->assignRole($superAdminRole);

    expect($superAdmin->can('any-unregistered-permission'))->toBeTrue();
});

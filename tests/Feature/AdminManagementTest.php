<?php

use App\Models\ServiceProviderProfile;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('admin can view vendor approvals page', function () {
    $admin = User::where('email', 'admin@heyevents.com')->first();
    $this->actingAs($admin);

    $response = $this->get(route('admin.vendors.index'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('admin/VendorApprovals')
        ->has('vendors')
        ->has('counts')
    );
});

test('client cannot view vendor approvals page', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    $response = $this->get(route('admin.vendors.index'));
    $response->assertForbidden();
});

test('admin can view user management page', function () {
    $admin = User::where('email', 'admin@heyevents.com')->first();
    $this->actingAs($admin);

    $response = $this->get(route('admin.users.index'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('admin/Users')
        ->has('users')
        ->has('stats')
        ->has('availableRoles')
    );
});

test('client cannot view user management page', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    $response = $this->get(route('admin.users.index'));
    $response->assertForbidden();
});

test('admin can change role of another user', function () {
    $admin = User::where('email', 'admin@heyevents.com')->first();
    $targetUser = User::factory()->create();
    $targetUser->assignRole('Client');

    $this->actingAs($admin);

    $response = $this->put(route('admin.users.update-role', $targetUser->id), [
        'role' => 'Service Provider',
    ]);

    $response->assertRedirect();
    $targetUser->refresh();
    expect($targetUser->hasRole('Service Provider'))->toBeTrue();
    expect($targetUser->hasRole('Client'))->toBeFalse();
});

test('standard admin cannot promote user to super admin', function () {
    $admin = User::where('email', 'admin@heyevents.com')->first();
    $targetUser = User::factory()->create();
    $targetUser->assignRole('Client');

    $this->actingAs($admin);

    $response = $this->put(route('admin.users.update-role', $targetUser->id), [
        'role' => 'Super Admin',
    ]);

    $response->assertForbidden();
    $targetUser->refresh();
    expect($targetUser->hasRole('Super Admin'))->toBeFalse();
});

test('super admin can promote user to super admin', function () {
    $superAdmin = User::where('email', 'superadmin@heyevents.com')->first();
    $targetUser = User::factory()->create();
    $targetUser->assignRole('Client');

    $this->actingAs($superAdmin);

    $response = $this->put(route('admin.users.update-role', $targetUser->id), [
        'role' => 'Super Admin',
    ]);

    $response->assertRedirect();
    $targetUser->refresh();
    expect($targetUser->hasRole('Super Admin'))->toBeTrue();
});

test('super admin cannot delete their own account', function () {
    $superAdmin = User::where('email', 'superadmin@heyevents.com')->first();
    $this->actingAs($superAdmin);

    $response = $this->delete(route('admin.users.destroy', $superAdmin->id));
    $response->assertRedirect();
    expect(User::find($superAdmin->id))->not->toBeNull();
});

test('super admin can delete a standard user', function () {
    $superAdmin = User::where('email', 'superadmin@heyevents.com')->first();
    $targetUser = User::factory()->create();
    $this->actingAs($superAdmin);

    $response = $this->delete(route('admin.users.destroy', $targetUser->id));
    $response->assertRedirect();
    expect(User::find($targetUser->id))->toBeNull();
});

test('standard admin without delete permission cannot delete a user', function () {
    $admin = User::where('email', 'admin@heyevents.com')->first();
    $targetUser = User::factory()->create();
    $this->actingAs($admin);

    $response = $this->delete(route('admin.users.destroy', $targetUser->id));
    $response->assertForbidden();
    expect(User::find($targetUser->id))->not->toBeNull();
});

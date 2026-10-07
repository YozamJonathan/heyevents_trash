<?php

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests are redirected to the login page when accessing dashboard', function () {
    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('login'));
});

test('super admin accesses the unified dashboard', function () {
    $user = User::where('email', 'superadmin@heyevent.com')->first();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('auth.user.roles', ['Super Admin'])
    );
});

test('admin accesses the unified dashboard', function () {
    $user = User::where('email', 'admin@heyevent.com')->first();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('auth.user.roles', ['Admin'])
    );
});

test('service provider accesses the unified dashboard', function () {
    $user = User::where('email', 'provider@heyevent.com')->first();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('auth.user.roles', ['Service Provider'])
    );
});

test('client accesses the unified dashboard', function () {
    $user = User::where('email', 'client@heyevent.com')->first();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->where('auth.user.roles', ['Client'])
    );
});

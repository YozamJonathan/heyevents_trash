<?php

use App\Models\User;
use App\Notifications\ClientBookingNotification;
use Database\Seeders\RolesAndPermissionsSeeder;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('authenticated user can poll latest notifications without page refresh', function () {
    $admin = User::role('Admin')->first();
    $this->actingAs($admin);

    $admin->notify(new ClientBookingNotification(
        title: 'Real-time Test Alert',
        message: 'A client just submitted a booking request.',
        type: 'client_booking',
        actionUrl: '/admin/bookings'
    ));

    $response = $this->getJson(route('notifications.latest'));

    $response->assertOk();
    $response->assertJsonStructure([
        'unread_count',
        'notifications',
    ]);
    expect($response->json('unread_count'))->toBeGreaterThanOrEqual(1);
    expect($response->json('notifications.0.data.title'))->toBe('Real-time Test Alert');
});

test('unauthenticated visitors cannot access latest notifications endpoint', function () {
    $response = $this->getJson(route('notifications.latest'));
    $response->assertUnauthorized();
});

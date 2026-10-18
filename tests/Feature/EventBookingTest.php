<?php

use App\Models\EventBooking;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('guests are redirected to login when trying to view booking form', function () {
    $response = $this->get(route('events.book'));
    $response->assertRedirect(route('login'));
});

test('authenticated client can view the event booking form', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    $response = $this->get(route('events.book'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('events/Book')
    );
});

test('client can submit a booking request and it persists to database with confirmation flash', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    $response = $this->post(route('events.book.store'), [
        'name' => 'John & Sarah Wedding Anniversary',
        'event_type' => 'Wedding Celebration',
        'services' => ['cake', 'music_dj', 'photography'],
        'budget' => null,
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Thanks! The team will reach you soon.');
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('event_bookings', [
        'user_id' => $client->id,
        'name' => 'John & Sarah Wedding Anniversary',
        'event_type' => 'Wedding Celebration',
        'status' => 'Pending Review',
    ]);
});

test('client can submit a booking request with a specified TZS budget', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    $response = $this->post(route('events.book.store'), [
        'name' => 'Corporate Tech Gala 2026',
        'event_type' => 'Corporate Gala & Banquet',
        'services' => ['transportation', 'decoration', 'card', 'mc'],
        'budget' => 3500000,
        'location' => 'Dar es Salaam',
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('success', 'Thanks! The team will reach you soon.');
    $response->assertSessionHasNoErrors();

    $this->assertDatabaseHas('event_bookings', [
        'user_id' => $client->id,
        'name' => 'Corporate Tech Gala 2026',
        'budget' => 3500000.00,
    ]);
});

test('client can view my booked events page', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    // Create a sample booking
    $client->eventBookings()->create([
        'name' => 'Beachside Birthday Party',
        'event_type' => 'Birthday & Anniversary Party',
        'services' => ['cake', 'music_dj'],
        'budget' => 1200000,
        'status' => 'Pending Review',
    ]);

    $response = $this->get(route('events.my-bookings'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('events/MyBookings')
        ->has('bookings', 1)
        ->where('bookings.0.name', 'Beachside Birthday Party')
    );
});

test('client dashboard displays booked events', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    $client->eventBookings()->create([
        'name' => 'Annual Corporate Banquet',
        'event_type' => 'Corporate Gala & Banquet',
        'services' => ['mc', 'decoration'],
        'budget' => 5000000,
        'status' => 'Pending Review',
    ]);

    $response = $this->get(route('dashboard'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('Dashboard')
        ->has('booked_events', 1)
        ->where('booked_events.0.name', 'Annual Corporate Banquet')
    );
});

test('booking submission requires name, event_type, and at least one service', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    $response = $this->post(route('events.book.store'), [
        'name' => '',
        'event_type' => '',
        'services' => [],
    ]);

    $response->assertSessionHasErrors(['name', 'event_type', 'services']);
});

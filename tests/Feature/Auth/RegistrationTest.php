<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('short passwords use the selected Swahili locale', function () {
    $response = $this->withUnencryptedCookie('heyevents_locale', 'sw')->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
    ]);

    $response->assertSessionHasErrors([
        'password' => 'Sehemu ya nenosiri lazima iwe na angalau herufi 8.',
    ]);
});

test('incomplete email ending without domain extension is rejected', function (string $invalidEmail) {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => $invalidEmail,
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors(['email']);
})->with([
    'test@gmail',
    'user@yahoo',
    'someone@domain.',
]);

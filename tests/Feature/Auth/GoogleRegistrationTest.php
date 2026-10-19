<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

function mockGoogleUser(array $attributes): void
{
    $provider = Mockery::mock(Provider::class);
    $provider->shouldReceive('user')->once()->andReturn(GoogleUser::fake($attributes));

    Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
}

test('google sign-up creates a verified account and signs the user in', function () {
    mockGoogleUser([
        'id' => 'google-user-100',
        'name' => 'Google User',
        'email' => 'google-user@example.com',
        'email_verified' => true,
    ]);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'google-user@example.com',
        'google_id' => 'google-user-100',
    ]);
    expect(User::query()->where('email', 'google-user@example.com')->first()->email_verified_at)->not->toBeNull();
});

test('google sign-up links a verified existing email without changing its password', function () {
    $user = User::factory()->create([
        'email' => 'existing-user@example.com',
        'password' => 'existing-password',
    ]);
    $passwordHash = $user->getRawOriginal('password');

    mockGoogleUser([
        'id' => 'google-user-200',
        'name' => 'Existing User',
        'email' => 'existing-user@example.com',
        'email_verified' => true,
    ]);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticatedAs($user);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'google_id' => 'google-user-200',
    ]);
    expect($user->fresh()->getRawOriginal('password'))->toBe($passwordHash);
    expect(Hash::check('existing-password', $user->fresh()->password))->toBeTrue();
    expect(User::query()->where('email', 'existing-user@example.com')->count())->toBe(1);
});

test('google sign-up rejects an unverified email address', function () {
    mockGoogleUser([
        'id' => 'google-user-300',
        'name' => 'Unverified User',
        'email' => 'unverified@example.com',
        'email_verified' => false,
    ]);

    $response = $this->get(route('google.callback'));

    $response->assertRedirect(route('register', absolute: false));
    $response->assertSessionHasErrors('google');
    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'unverified@example.com']);
});

test('google redirect explains when oauth credentials are missing', function () {
    config([
        'services.google.client_id' => null,
        'services.google.client_secret' => null,
    ]);

    $response = $this->get(route('google.redirect'));

    $response->assertRedirect(route('register', absolute: false));
    $response->assertSessionHasErrors('google');
});

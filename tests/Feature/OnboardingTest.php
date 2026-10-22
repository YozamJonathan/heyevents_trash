<?php

use App\Models\ServiceProviderProfile;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
    Storage::fake('public');
});

test('guests are redirected to the login page from onboarding', function () {
    $response = $this->get(route('onboarding.index'));
    $response->assertRedirect(route('login'));
});

test('users without a role are redirected from dashboard to onboarding', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertRedirect(route('onboarding.index'));
});

test('users without a role can view the onboarding screen', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('onboarding.index'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('onboarding/SelectRole')
    );
});

test('users with an assigned role are redirected from onboarding to dashboard', function () {
    $client = User::where('email', 'client@heyevents.com')->first();
    $this->actingAs($client);

    $response = $this->get(route('onboarding.index'));
    $response->assertRedirect(route('dashboard'));
});

test('user selecting client receives Client role and is redirected to dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    expect($user->roles()->count())->toBe(0);

    $response = $this->post(route('onboarding.client'));
    $response->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->hasRole('Client'))->toBeTrue();
});

test('service provider can submit KYC and receive pending status', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('business_licence.pdf', 500, 'application/pdf');

    $response = $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio & Visuals',
        'contact_name' => 'Baraka John',
        'nida_number' => '19950101-12345-00001-21',
        'social_media' => '@safariaudio_tz',
        'category' => 'Music & DJ',
        'description' => 'Professional sound system and event lighting.',
        'business_licence' => $file,
    ]);

    $response->assertRedirect(route('dashboard'));

    $user->refresh();
    expect($user->hasRole('Service Provider'))->toBeTrue();

    $profile = $user->serviceProviderProfile;
    expect($profile)->not->toBeNull();
    expect($profile->brand_name)->toBe('Safari Audio & Visuals');
    expect($profile->contact_name)->toBe('Baraka John');
    expect($profile->nida_number)->toBe('19950101-12345-00001-21');
    expect($profile->social_media)->toBe('@safariaudio_tz');
    expect($profile->status)->toBe('pending');
    expect($profile->business_licence_path)->not->toBeNull();

    Storage::disk('public')->assertExists($profile->business_licence_path);
});

test('admin can approve pending vendor KYC profile', function () {
    $admin = User::where('email', 'admin@heyevents.com')->first();
    $providerUser = User::factory()->create();
    $providerUser->assignRole('Service Provider');

    $profile = ServiceProviderProfile::create([
        'user_id' => $providerUser->id,
        'brand_name' => 'Kilimanjaro MCs',
        'contact_name' => 'Moses Peter',
        'nida_number' => '19920101-54321-00002-19',
        'social_media' => '@kilimanjaromcs',
        'category' => 'Master of Ceremony (MC)',
        'business_licence_path' => 'vendor_licences/fake.pdf',
        'status' => 'pending',
    ]);

    $this->actingAs($admin);

    $response = $this->post(route('admin.vendors.approve', $profile->id));
    $response->assertRedirect();

    $profile->refresh();
    expect($profile->status)->toBe('approved');
    expect($profile->verified_at)->not->toBeNull();
    expect($profile->verified_by)->toBe($admin->id);
});

test('admin can reject pending vendor KYC profile with reason', function () {
    $admin = User::where('email', 'admin@heyevents.com')->first();
    $providerUser = User::factory()->create();
    $providerUser->assignRole('Service Provider');

    $profile = ServiceProviderProfile::create([
        'user_id' => $providerUser->id,
        'brand_name' => 'Zanzibar Catering',
        'contact_name' => 'Amina Said',
        'nida_number' => '19940101-99999-00003-25',
        'social_media' => '@zanzibarcaterers',
        'category' => 'Catering & Cake',
        'business_licence_path' => 'vendor_licences/fake.pdf',
        'status' => 'pending',
    ]);

    $this->actingAs($admin);

    $response = $this->post(route('admin.vendors.reject', $profile->id), [
        'reason' => 'Business licence document is blurry and unreadable.',
    ]);
    $response->assertRedirect();

    $profile->refresh();
    expect($profile->status)->toBe('rejected');
    expect($profile->rejection_reason)->toBe('Business licence document is blurry and unreadable.');
});

test('rejected vendor can access onboarding screen to re-submit documents', function () {
    $providerUser = User::factory()->create();
    $providerUser->assignRole('Service Provider');

    ServiceProviderProfile::create([
        'user_id' => $providerUser->id,
        'brand_name' => 'Zanzibar Catering',
        'contact_name' => 'Amina Said',
        'nida_number' => '19940101-99999-00003-25',
        'social_media' => '@zanzibarcaterers',
        'category' => 'Catering & Cake',
        'business_licence_path' => 'vendor_licences/fake.pdf',
        'status' => 'rejected',
        'rejection_reason' => 'Licence expired',
    ]);

    $this->actingAs($providerUser);

    $response = $this->get(route('onboarding.index'));
    $response->assertOk();
    $response->assertInertia(fn (Assert $page) => $page
        ->component('onboarding/SelectRole')
        ->has('existingProfile')
        ->where('existingProfile.status', 'rejected')
    );
});

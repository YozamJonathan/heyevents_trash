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
        'nida_number' => '19950101123450000121',
        'primary_phone' => '+255754111222',
        'secondary_phone' => '0713333444',
        'categories' => ['Music & DJ', 'Sound & Lighting'],
        'instagram' => '@safariaudio_tz',
        'whatsapp' => '+255754111222',
        'tiktok' => '@safariaudio',
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
    expect($profile->nida_number)->toBe('19950101123450000121');
    expect($profile->primary_phone)->toBe('+255754111222');
    expect($profile->secondary_phone)->toBe('0713333444');
    expect($profile->categories)->toEqual(['Music & DJ', 'Sound & Lighting']);
    expect($profile->instagram)->toBe('@safariaudio_tz');
    expect($profile->whatsapp)->toBe('+255754111222');
    expect($profile->tiktok)->toBe('@safariaudio');
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

test('provider KYC submission fails when required fields or categories are missing', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->post(route('onboarding.provider'), [
        'brand_name' => '',
        'contact_name' => '',
        'nida_number' => '',
        'primary_phone' => '',
        'categories' => [],
    ]);

    $response->assertSessionHasErrors([
        'brand_name',
        'contact_name',
        'nida_number',
        'primary_phone',
        'categories',
        'business_licence',
    ]);
});

test('rejected provider can resubmit KYC with existing licence and updated details', function () {
    $user = User::factory()->create();
    $user->assignRole('Service Provider');

    ServiceProviderProfile::create([
        'user_id' => $user->id,
        'brand_name' => 'Original Brand',
        'contact_name' => 'Original Contact',
        'nida_number' => '19900101111122222333',
        'primary_phone' => '+255711000000',
        'categories' => ['Music & DJ'],
        'category' => 'Music & DJ',
        'business_licence_path' => 'vendor_licences/existing.pdf',
        'status' => 'rejected',
        'rejection_reason' => 'Please provide more details on services.',
    ]);

    $this->actingAs($user);

    $response = $this->post(route('onboarding.provider'), [
        'brand_name' => 'Updated Brand Name',
        'contact_name' => 'Updated Contact Name',
        'nida_number' => '19900101111122222333',
        'primary_phone' => '0711999888',
        'categories' => ['Music & DJ', 'Sound & Lighting'],
        'instagram' => '@updated_insta',
        'description' => 'Comprehensive audio and visual equipment provider.',
    ]);

    $response->assertRedirect(route('dashboard'));

    $profile = $user->fresh()->serviceProviderProfile;
    expect($profile->brand_name)->toBe('Updated Brand Name');
    expect($profile->contact_name)->toBe('Updated Contact Name');
    expect($profile->primary_phone)->toBe('0711999888');
    expect($profile->categories)->toEqual(['Music & DJ', 'Sound & Lighting']);
    expect($profile->business_licence_path)->toBe('vendor_licences/existing.pdf');
    expect($profile->status)->toBe('pending');
    expect($profile->rejection_reason)->toBeNull();
});

test('provider KYC rejects hyphenated or short NIDA numbers', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('business_licence.pdf', 500, 'application/pdf');

    $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio',
        'contact_name' => 'Baraka John',
        'nida_number' => '19900101-12345-00001-20',
        'primary_phone' => '+255712345678',
        'categories' => ['Music & DJ'],
        'business_licence' => $file,
    ])->assertSessionHasErrors('nida_number');

    $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio',
        'contact_name' => 'Baraka John',
        'nida_number' => '19900101123456789',
        'primary_phone' => '+255712345678',
        'categories' => ['Music & DJ'],
        'business_licence' => $file,
    ])->assertSessionHasErrors('nida_number');
});

test('provider KYC rejects phone numbers that do not match +255 or 0 rules', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('business_licence.pdf', 500, 'application/pdf');

    $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio',
        'contact_name' => 'Baraka John',
        'nida_number' => '19900101123456789012',
        'primary_phone' => '+255 712 345 678',
        'categories' => ['Music & DJ'],
        'business_licence' => $file,
    ])->assertSessionHasErrors('primary_phone');

    $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio',
        'contact_name' => 'Baraka John',
        'nida_number' => '19900101123456789012',
        'primary_phone' => '+25571234567',
        'categories' => ['Music & DJ'],
        'business_licence' => $file,
    ])->assertSessionHasErrors('primary_phone');

    $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio',
        'contact_name' => 'Baraka John',
        'nida_number' => '19900101123456789012',
        'primary_phone' => '071234567',
        'categories' => ['Music & DJ'],
        'business_licence' => $file,
    ])->assertSessionHasErrors('primary_phone');
});

test('provider KYC validates social handles, whatsapp, website and description', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('business_licence.pdf', 500, 'application/pdf');

    $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio',
        'contact_name' => 'Baraka John',
        'nida_number' => '19900101123456789012',
        'primary_phone' => '+255712345678',
        'categories' => ['Music & DJ'],
        'instagram' => 'not a handle!',
        'whatsapp' => 'https://example.com/chat',
        'tiktok' => '@a b',
        'social_media' => 'not a url',
        'description' => str_repeat('a', 2001),
        'business_licence' => $file,
    ])->assertSessionHasErrors(['instagram', 'whatsapp', 'tiktok', 'social_media', 'description']);
});

test('provider KYC rejects unknown categories, duplicate phones and invalid contact names', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $file = UploadedFile::fake()->create('business_licence.pdf', 500, 'application/pdf');

    $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio',
        'contact_name' => 'Baraka 123',
        'nida_number' => '19900101123456789012',
        'primary_phone' => '0712345678',
        'secondary_phone' => '0712345678',
        'categories' => ['Hacking Services'],
        'business_licence' => $file,
    ])->assertSessionHasErrors(['contact_name', 'secondary_phone', 'categories.0']);
});

test('provider KYC rejects licence files with an unsupported type or size', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $payload = [
        'brand_name' => 'Safari Audio',
        'contact_name' => 'Baraka John',
        'nida_number' => '19900101123456789012',
        'primary_phone' => '0712345678',
        'categories' => ['Music & DJ'],
    ];

    $this->post(route('onboarding.provider'), $payload + [
        'business_licence' => UploadedFile::fake()->create('licence.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'),
    ])->assertSessionHasErrors('business_licence');

    $this->post(route('onboarding.provider'), $payload + [
        'business_licence' => UploadedFile::fake()->create('licence.pdf', 6000, 'application/pdf'),
    ])->assertSessionHasErrors('business_licence');
});

test('provider KYC accepts social links and normalises a website without scheme', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->post(route('onboarding.provider'), [
        'brand_name' => 'Safari Audio',
        'contact_name' => "Neema O'Brien-Mushi",
        'nida_number' => '19900101123456789012',
        'primary_phone' => '0712345678',
        'categories' => ['Music & DJ', 'Venue & Hall'],
        'instagram' => 'https://www.instagram.com/safariaudio_tz',
        'whatsapp' => 'https://wa.me/255712345678',
        'tiktok' => 'https://www.tiktok.com/@safariaudio',
        'social_media' => 'safariaudio.co.tz',
        'business_licence' => UploadedFile::fake()->create('licence.png', 300, 'image/png'),
    ])->assertSessionHasNoErrors()->assertRedirect(route('dashboard'));

    $profile = $user->fresh()->serviceProviderProfile;
    expect($profile->social_media)->toBe('https://safariaudio.co.tz');
    expect($profile->whatsapp)->toBe('https://wa.me/255712345678');
    expect($profile->categories)->toEqual(['Music & DJ', 'Venue & Hall']);
});

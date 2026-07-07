<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Account;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Region;
use App\Models\User;

test('a guest cannot create addresses for contacts and accounts', function () {
    $contact = Contact::factory()->create();
    $account = Account::factory()->create();

    $addressData = [
        'name' => 'Address 1',
        'line1' => 'Place 123',
        'line2' => null,
        'city' => 'Bizerte',
        'region_id' => 'tn-23',
        'country_id' => 'tn',
        'postal_code' => '7000',
    ];

    $this->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectToRoute('login');

    $this->post(route('addresses.store.for.account', $account), $addressData)
        ->assertRedirectToRoute('login');

    $this->assertDatabaseCount('addresses', 0);
});

test('a non-verified user cannot create addresses for contacts and accounts', function () {
    $nonVerifiedUser = User::factory()->unverified()->create();
    $contact = Contact::factory()->create();
    $account = Account::factory()->create();

    $addressData = [
        'name' => 'Address 1',
        'line1' => 'Place 123',
        'line2' => null,
        'city' => 'Bizerte',
        'region_id' => 'tn-23',
        'country_id' => 'tn',
        'postal_code' => '7000',
    ];

    $this->actingAs($nonVerifiedUser)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectToRoute('verification.notice');

    $this
        ->actingAs($nonVerifiedUser)
        ->post(route('addresses.store.for.account', $account), $addressData)
        ->assertRedirectToRoute('verification.notice');

    $this->assertDatabaseCount('addresses', 0);
});

test('a user without permissions cannot create addresses for contacts and accounts', function () {
    $user = User::factory()
        ->create()
        ->syncRoles([UserRole::User->value]);
    $contact = Contact::factory()->create();
    $account = Account::factory()->create();

    $addressData = [
        'name' => 'Address 1',
        'line1' => 'Place 123',
        'line2' => null,
        'city' => 'Bizerte',
        'region_id' => 'tn-23',
        'country_id' => 'tn',
        'postal_code' => '7000',
    ];

    $this->actingAs($user)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertForbidden();

    $this
        ->actingAs($user)
        ->post(route('addresses.store.for.account', $account), $addressData)
        ->assertForbidden();

    $this->assertDatabaseCount('addresses', 0);
});

test('addresses can be added to contacts and accounts', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $users = [
        $superAdmin,
        $manager,
        $salesAgent,
    ];

    $addressesCount = 0;

    foreach ($users as $user) {
        $addressesCount += 2;

        $contact = Contact::factory()->create([
            'user_id' => $user->id,
        ]);
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);
        $region = Region::query()
            ->inRandomOrder()
            ->first();

        $addressData = [
            'name' => fake()->sentence(),
            'line1' => fake()->streetAddress(),
            'line2' => fake()->streetAddress(),
            'city' => fake()->word(),
            'region_id' => $region->id,
            'country_id' => $region->country_id,
            'postal_code' => fake()->postcode(),
        ];

        $this->actingAs($user)
            ->fromRoute('contacts.edit', $contact)
            ->post(route('addresses.store.for.contact', $contact), [
                ...$addressData,
                'name' => $addressData['name'].' contact '.$contact->id,
            ])
            ->assertRedirectBack()
            ->assertSessionHas('success', 'The address has been added.');

        $this->actingAs($user)
            ->fromRoute('accounts.edit', $account)
            ->post(route('addresses.store.for.account', $account), [
                ...$addressData,
                'name' => $addressData['name'].' account '.$account->id,
            ])
            ->assertRedirectBack()
            ->assertSessionHas('success', 'The address has been added.');

        $this->assertDatabaseCount('addresses', $addressesCount);

        $contactAddress = $contact->address()
            ->orderByDesc('id')
            ->first();

        $accountAddress = $account->addresses()
            ->orderByDesc('id')
            ->first();

        expect($contactAddress->addressable)->toBeInstanceOf(Contact::class)
            ->and($contactAddress->addressable->id)->toBe($contact->id)
            ->and($contactAddress->name)->toBe($addressData['name'].' contact '.$contact->id)
            ->and($contactAddress->line1)->toBe($addressData['line1'])
            ->and($contactAddress->line2)->toBe($addressData['line2'])
            ->and($contactAddress->city)->toBe($addressData['city'])
            ->and($contactAddress->region_id)->toBe($addressData['region_id'])
            ->and($contactAddress->country_id)->toBe($addressData['country_id'])
            ->and($contactAddress->postal_code)->toBe($addressData['postal_code']);

        expect($accountAddress->addressable)->toBeInstanceOf(Account::class)
            ->and($accountAddress->addressable->id)->toBe($account->id)
            ->and($accountAddress->name)->toBe($addressData['name'].' account '.$account->id)
            ->and($accountAddress->line1)->toBe($addressData['line1'])
            ->and($accountAddress->line2)->toBe($addressData['line2'])
            ->and($accountAddress->city)->toBe($addressData['city'])
            ->and($accountAddress->region_id)->toBe($addressData['region_id'])
            ->and($accountAddress->country_id)->toBe($addressData['country_id'])
            ->and($accountAddress->postal_code)->toBe($addressData['postal_code']);
    }
});

test('a contact can only have one address', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    Address::factory()->forContact($contact)->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->assertDatabaseCount('addresses', 1);

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'name' => 'The address already exists.',
        ]);

    $this->assertDatabaseCount('addresses', 1);
});

test('an account can have multiple addresses', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $account = Account::factory()->create();
    Address::factory()->forAccount($account)->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->assertDatabaseCount('addresses', 1);

    $this->actingAs($superAdmin)
        ->fromRoute('accounts.edit', $account)
        ->post(route('addresses.store.for.account', $account), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been added.');

    $this->assertDatabaseCount('addresses', 2);
});

test('a sales agent cannot create an address on a contact or account owned by another user', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $account = Account::factory()->create();
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();
    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.edit', $account)
        ->post(route('addresses.store.for.account', $account), $addressData)
        ->assertForbidden();

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertForbidden();

    $this->assertDatabaseCount('addresses', 0);
});

test('line2 is optional', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $account = Account::factory()->create();
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => null,
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('accounts.edit', $account)
        ->post(route('addresses.store.for.account', $account), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been added.');

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been added.');

    $this->assertDatabaseCount('addresses', 2);

    expect($account->addresses->first()->line2)->toBeNull()
        ->and($contact->address->line2)->toBeNull();
});

it('fails when the name is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => $invalidValue,
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'name' => $errorMessage,
        ]);

    $this->assertDatabaseCount('addresses', 0);
})->with('invalid-address-name');

it('fails when the line1 field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => $invalidValue,
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'line1' => $errorMessage,
        ]);

    $this->assertDatabaseCount('addresses', 0);
})->with('invalid-address-line1');

it('fails when the line2 field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => $invalidValue,
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'line2' => $errorMessage,
        ]);

    $this->assertDatabaseCount('addresses', 0);
})->with('invalid-address-line2');

it('fails when the city field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => $invalidValue,
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'city' => $errorMessage,
        ]);

    $this->assertDatabaseCount('addresses', 0);
})->with('invalid-address-city');

it('fails when the region field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $invalidValue,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'region_id' => $errorMessage,
        ]);

    $this->assertDatabaseCount('addresses', 0);
})->with('invalid-address-region-id');

it('fails when the country field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'country_id' => $invalidValue,
        'region_id' => $region->id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'country_id' => $errorMessage,
        ]);

    $this->assertDatabaseCount('addresses', 0);
})->with('invalid-address-country-id');

it('fails when the postal code field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'country_id' => $region->country_id,
        'region_id' => $region->id,
        'postal_code' => $invalidValue,
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'postal_code' => $errorMessage,
        ]);

    $this->assertDatabaseCount('addresses', 0);
})->with('invalid-address-postal-code');

it('fails when the region selected does not belongs to the selected country', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();

    $addressData = [
        'name' => fake()->sentence(),
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'country_id' => 'tn',
        'region_id' => 'us-123',
        'postal_code' => '1234',
    ];

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'region_id' => 'The state field is invalid.',
        ]);

    $this->assertDatabaseCount('addresses', 0);
});

test('creating addresses for contacts is rate limited', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();

    exhaustRateLimit('addresses-manage', (string) $superAdmin->id, 10);

    $addressData = [
        'name' => 'Address 1',
        'line1' => 'Place 123',
        'line2' => null,
        'city' => 'Bizerte',
        'region_id' => 'tn-23',
        'country_id' => 'tn',
        'postal_code' => '7000',
    ];

    $this->actingAs($superAdmin)
        ->post(route('addresses.store.for.contact', $contact), $addressData)
        ->assertTooManyRequests();

    $this->assertDatabaseCount('addresses', 0);
});

test('creating addresses for accounts is rate limited', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $account = Account::factory()->create();

    exhaustRateLimit('addresses-manage', (string) $superAdmin->id, 10);

    $addressData = [
        'name' => 'Address 1',
        'line1' => 'Place 123',
        'line2' => null,
        'city' => 'Bizerte',
        'region_id' => 'tn-23',
        'country_id' => 'tn',
        'postal_code' => '7000',
    ];

    $this->actingAs($superAdmin)
        ->post(route('addresses.store.for.account', $account), $addressData)
        ->assertTooManyRequests();

    $this->assertDatabaseCount('addresses', 0);
});

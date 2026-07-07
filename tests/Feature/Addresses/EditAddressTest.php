<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Account;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Region;
use App\Models\User;

test('a guest cannot update addresses for contacts and accounts', function () {
    $addressData = [
        'name' => 'Address 1',
        'line1' => 'Place 123',
        'line2' => null,
        'city' => 'Bizerte',
        'region_id' => 'tn-23',
        'country_id' => 'tn',
        'postal_code' => '7000',
    ];

    $this->patch(route('addresses.update.for.contact', ['contact' => 123, 'address' => 123]), $addressData)
        ->assertRedirectToRoute('login');

    $this->patch(route('addresses.update.for.account', ['account' => 1234, 'address' => 1234]), $addressData)
        ->assertRedirectToRoute('login');
});

test('a non-verified user cannot update addresses for contacts and accounts', function () {
    $nonVerifiedUser = User::factory()->unverified()->create();
    $contact = Contact::factory()->create();
    $account = Account::factory()->create();
    $contactAddress = Address::factory()->forContact($contact)->create();
    $accountAddress = Address::factory()->forAccount($account)->create();

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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $contactAddress]), $addressData)
        ->assertRedirectToRoute('verification.notice');

    $this->actingAs($nonVerifiedUser)
        ->patch(route('addresses.update.for.account', ['account' => $account, 'address' => $accountAddress]), $addressData)
        ->assertRedirectToRoute('verification.notice');
});

test('a user without permissions cannot update addresses for contacts and accounts', function () {
    $user = User::factory()
        ->create()
        ->syncRoles([UserRole::User->value]);
    $contact = Contact::factory()->create();
    $account = Account::factory()->create();
    $contactAddress = Address::factory()->forContact($contact)->create();
    $accountAddress = Address::factory()->forAccount($account)->create();

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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $contactAddress]), $addressData)
        ->assertForbidden();

    $this->actingAs($user)
        ->patch(route('addresses.update.for.account', ['account' => $account, 'address' => $accountAddress]), $addressData)
        ->assertForbidden();
});

test('super admin can update addresses for contacts and accounts', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $account = Account::factory()->create();
    $contactAddress = Address::factory()->forContact($contact)->create();
    $accountAddress = Address::factory()->forAccount($account)->create();
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

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $contactAddress]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been updated.');

    $this->actingAs($superAdmin)
        ->fromRoute('accounts.edit', $account)
        ->patch(route('addresses.update.for.account', ['account' => $account, 'address' => $accountAddress]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been updated.');

    $contactAddress->refresh();
    $accountAddress->refresh();

    expect($contactAddress->addressable->id)->toBe($contact->id)
        ->and($contactAddress->name)->toBe($addressData['name'])
        ->and($contactAddress->line1)->toBe($addressData['line1'])
        ->and($contactAddress->line2)->toBe($addressData['line2'])
        ->and($contactAddress->city)->toBe($addressData['city'])
        ->and($contactAddress->region_id)->toBe($addressData['region_id'])
        ->and($contactAddress->country_id)->toBe($addressData['country_id'])
        ->and($contactAddress->postal_code)->toBe($addressData['postal_code']);

    expect($accountAddress->addressable->id)->toBe($account->id)
        ->and($accountAddress->name)->toBe($addressData['name'])
        ->and($accountAddress->line1)->toBe($addressData['line1'])
        ->and($accountAddress->line2)->toBe($addressData['line2'])
        ->and($accountAddress->city)->toBe($addressData['city'])
        ->and($accountAddress->region_id)->toBe($addressData['region_id'])
        ->and($accountAddress->country_id)->toBe($addressData['country_id'])
        ->and($accountAddress->postal_code)->toBe($addressData['postal_code']);
});

test('managers can update their addresses and others', function () {
    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $ownContact = Contact::factory()->create(['user_id' => $manager->id]);
    $ownAddress = Address::factory()->forContact($ownContact)->create();

    $otherContact = Contact::factory()->create();
    $otherAddress = Address::factory()->forContact($otherContact)->create();

    expect($ownContact->user_id)->not()->toBe($otherContact->user_id);

    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => 'Updated by manager',
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($manager)
        ->fromRoute('contacts.edit', $ownContact)
        ->patch(route('addresses.update.for.contact', ['contact' => $ownContact, 'address' => $ownAddress]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been updated.');

    $this->actingAs($manager)
        ->fromRoute('contacts.edit', $otherContact)
        ->patch(route('addresses.update.for.contact', ['contact' => $otherContact, 'address' => $otherAddress]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been updated.');

    expect($ownAddress->refresh()->name)->toBe('Updated by manager')
        ->and($otherAddress->refresh()->name)->toBe('Updated by manager');
});

test('sales agents can update their own addresses', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create(['user_id' => $salesAgent->id]);
    $contactAddress = Address::factory()->forContact($contact)->create();

    $account = Account::factory()->create(['user_id' => $salesAgent->id]);
    $accountAddress = Address::factory()->forAccount($account)->create();

    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => 'Updated by sales agent',
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $contactAddress]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been updated.');

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.edit', $account)
        ->patch(route('addresses.update.for.account', ['account' => $account, 'address' => $accountAddress]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been updated.');

    expect($contactAddress->refresh()->name)->toBe('Updated by sales agent')
        ->and($accountAddress->refresh()->name)->toBe('Updated by sales agent');
});

test('sales agents cannot update addresses owned by another user', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create();
    $account = Account::factory()->create();
    $contactAddress = Address::factory()->forContact($contact)->create();
    $accountAddress = Address::factory()->forAccount($account)->create();

    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => 'Should not be applied',
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $contactAddress]), $addressData)
        ->assertForbidden();

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.edit', $account)
        ->patch(route('addresses.update.for.account', ['account' => $account, 'address' => $accountAddress]), $addressData)
        ->assertForbidden();

    expect($contactAddress->refresh()->name)->not()->toBe('Should not be applied')
        ->and($accountAddress->refresh()->name)->not()->toBe('Should not be applied');
});

test('line2 can be cleared when updating an address', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();

    expect($address->line2)->not()->toBeNull();

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
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been updated.');

    expect($address->refresh()->line2)->toBeNull();
});

it('fails when the name is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();
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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'name' => $errorMessage,
        ]);

    expect($address->refresh()->name)->not()->toBe($invalidValue);
})->with('invalid-address-name');

it('fails when the line1 field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();
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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'line1' => $errorMessage,
        ]);

    expect($address->refresh()->line1)->not()->toBe($invalidValue);
})->with('invalid-address-line1');

it('fails when the line2 field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();
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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'line2' => $errorMessage,
        ]);

    expect($address->refresh()->line2)->not()->toBe($invalidValue);
})->with('invalid-address-line2');

it('fails when the city field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();
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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'city' => $errorMessage,
        ]);

    expect($address->refresh()->city)->not()->toBe($invalidValue);
})->with('invalid-address-city');

it('fails when the region field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();
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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'region_id' => $errorMessage,
        ]);

    expect($address->refresh()->region_id)->not()->toBe($invalidValue);
})->with('invalid-address-region-id');

it('fails when the country field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();
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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'country_id' => $errorMessage,
        ]);

    expect($address->refresh()->country_id)->not()->toBe($invalidValue);
})->with('invalid-address-country-id');

it('fails when the postal code field is invalid', function ($invalidValue, string $errorMessage) {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();
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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'postal_code' => $errorMessage,
        ]);

    expect($address->refresh()->postal_code)->not()->toBe($invalidValue);
})->with('invalid-address-postal-code');

it('fails when the region selected does not belongs to the selected country', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();

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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'region_id' => 'The state field is invalid.',
        ]);

    expect($address->refresh()->country_id)->not()->toBe('tn');
});

test('cannot update an address that belongs to a different parent model', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contactA = Contact::factory()->create();
    $contactB = Contact::factory()->create(['user_id' => $salesAgent->id]);
    $addressB = Address::factory()->forContact($contactB)->create();

    $region = Region::query()
        ->inRandomOrder()
        ->first();

    $addressData = [
        'name' => 'Updated via mismatched parent',
        'line1' => fake()->streetAddress(),
        'line2' => fake()->streetAddress(),
        'city' => fake()->word(),
        'region_id' => $region->id,
        'country_id' => $region->country_id,
        'postal_code' => fake()->postcode(),
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contactA)
        ->patch(route('addresses.update.for.contact', ['contact' => $contactA, 'address' => $addressB]), $addressData)
        ->assertSessionHasErrors([
            'name' => 'The address does not belong to the contact.'
        ]);

    $addressB->refresh();

    expect($addressB->name)->not()->toBe('Updated via mismatched parent')
        ->and($addressB->addressable_id)->toBe($contactB->id);
});

test('updating addresses for contacts is rate limited', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();

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
        ->patch(route('addresses.update.for.contact', ['contact' => $contact, 'address' => $address]), $addressData)
        ->assertTooManyRequests();

    expect($address->refresh()->name)->not()->toBe('Address 1');
});

test('updating addresses for accounts is rate limited', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $account = Account::factory()->create();
    $address = Address::factory()->forAccount($account)->create();

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
        ->patch(route('addresses.update.for.account', ['account' => $account, 'address' => $address]), $addressData)
        ->assertTooManyRequests();

    expect($address->refresh()->name)->not()->toBe('Address 1');
});

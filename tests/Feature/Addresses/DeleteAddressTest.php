<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Account;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;

test('a guest cannot delete addresses', function () {
    $this->delete(route('addresses.destroy', 123))
        ->assertRedirectToRoute('login');
});

test('a non-verified user cannot delete addresses', function () {
    $nonVerifiedUser = User::factory()->unverified()->create();
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();

    $this->actingAs($nonVerifiedUser)
        ->delete(route('addresses.destroy', $address))
        ->assertRedirectToRoute('verification.notice');

    $this->assertDatabaseCount('addresses', 1);
});

test('a user without permissions cannot delete addresses', function () {
    $user = User::factory()
        ->create()
        ->syncRoles([UserRole::User->value]);
    $contact = Contact::factory()->create();
    $contactAddress = Address::factory()->forContact($contact)->create();

    $this->actingAs($user)
        ->delete(route('addresses.destroy', $contactAddress))
        ->assertForbidden();

    $this->assertDatabaseCount('addresses', 1);
});

test('super admin can delete contact and account addresses', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $account = Account::factory()->create();
    $contactAddress = Address::factory()->forContact($contact)->create();
    $accountAddress = Address::factory()->forAccount($account)->create();

    $this->actingAs($superAdmin)
        ->fromRoute('contacts.edit', $contact)
        ->delete(route('addresses.destroy', $contactAddress))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been deleted.');

    $this->assertDatabaseCount('addresses', 1);
    $this->assertDatabaseMissing('addresses', ['id' => $contactAddress->id]);

    $this->actingAs($superAdmin)
        ->fromRoute('accounts.edit', $account)
        ->delete(route('addresses.destroy', $accountAddress))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been deleted.');

    $this->assertDatabaseCount('addresses', 0);
});

test('managers can delete their addresses and others', function () {
    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $ownContact = Contact::factory()->create(['user_id' => $manager->id]);
    $ownAddress = Address::factory()->forContact($ownContact)->create();

    $otherContact = Contact::factory()->create();
    $otherAddress = Address::factory()->forContact($otherContact)->create();

    expect($ownContact->user_id)->not()->toBe($otherContact->user_id);

    $this->actingAs($manager)
        ->fromRoute('contacts.edit', $ownContact)
        ->delete(route('addresses.destroy', $ownAddress))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been deleted.');

    $this->actingAs($manager)
        ->fromRoute('contacts.edit', $otherContact)
        ->delete(route('addresses.destroy', $otherAddress))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been deleted.');

    $this->assertDatabaseCount('addresses', 0);
});

test('sales agents can delete their own addresses', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create(['user_id' => $salesAgent->id]);
    $contactAddress = Address::factory()->forContact($contact)->create();

    $account = Account::factory()->create(['user_id' => $salesAgent->id]);
    $accountAddress = Address::factory()->forAccount($account)->create();

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->delete(route('addresses.destroy', $contactAddress))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been deleted.');

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.edit', $account)
        ->delete(route('addresses.destroy', $accountAddress))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The address has been deleted.');

    $this->assertDatabaseCount('addresses', 0);
});

test('sales agents cannot delete addresses owned by another user', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create();
    $account = Account::factory()->create();
    $contactAddress = Address::factory()->forContact($contact)->create();
    $accountAddress = Address::factory()->forAccount($account)->create();

    $this->actingAs($salesAgent)
        ->delete(route('addresses.destroy', $contactAddress))
        ->assertForbidden();

    $this->actingAs($salesAgent)
        ->delete(route('addresses.destroy', $accountAddress))
        ->assertForbidden();

    $this->assertDatabaseCount('addresses', 2);
});

test('deleting addresses is rate limited', function () {
    $superAdmin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);
    $contact = Contact::factory()->create();
    $address = Address::factory()->forContact($contact)->create();

    exhaustRateLimit('addresses-manage', (string) $superAdmin->id, 10);

    $this->actingAs($superAdmin)
        ->delete(route('addresses.destroy', $address))
        ->assertTooManyRequests();

    $this->assertDatabaseCount('addresses', 1);
});

<?php

declare(strict_types=1);

use App\Enums\ContactStatus;
use App\Enums\UserRole;
use App\Models\Account;
use App\Models\Contact;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('redirects non-authenticated users to the login page', function () {
    $this->get(route('contacts.edit', 123))
        ->assertRedirectToRoute('login');

    $this->patch(route('contacts.update', 123))
        ->assertRedirectToRoute('login');
});

it('redirects to email verification page when email is not verified', function () {
    $user = User::factory()
        ->unverified()
        ->create();

    $contact = Contact::factory()
        ->create([
            'user_id' => $user->id,
        ]);

    $this->actingAs($user)
        ->get(route('contacts.edit', $contact))
        ->assertRedirectToRoute('verification.notice');

    $this->actingAs($user)
        ->patch(route('contacts.update', $contact))
        ->assertRedirectToRoute('verification.notice');
});

it('returns forbidden when user does not have the required permissions', function () {
    $user = User::factory()
        ->create();

    $contact = Contact::factory()
        ->create([
            'user_id' => $user->id,
        ]);

    $this->actingAs($user)
        ->get(route('contacts.edit', $contact))
        ->assertForbidden();

    $this->actingAs($user)
        ->patch(route('contacts.update', $contact))
        ->assertForbidden();
});

it('renders the edit contact page successfully', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $contact = Contact::factory()->create();

    $this->actingAs($admin)
        ->get(route('contacts.edit', $contact))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($contact) {
            $page->component('contacts/edit')
                ->has('contact')
                ->where('contact.id', $contact->id);
        });
});

test('admins can edit any contact', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $contact = Contact::factory()->create();
    $account = Account::factory()->create();

    $updatedData = [
        'first_name' => 'Updated first name',
        'last_name' => 'Updated last name',
        'email' => 'updated.email@test.cc',
        'phone' => '123-123-ABC',
        'status' => ContactStatus::Prospect->value,
        'account_id' => $account->id,
    ];

    $this->actingAs($admin)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), $updatedData)
        ->assertRedirectToRoute('contacts.index')
        ->assertSessionHas('success', 'The contact #'.$contact->id.' has been updated.');

    $this->assertDatabaseCount('contacts', 1);

    $contact->refresh();

    expect($contact->user_id)->not()->toBe($admin->id)
        ->and($contact->first_name)->toBe($updatedData['first_name'])
        ->and($contact->last_name)->toBe($updatedData['last_name'])
        ->and($contact->email)->toBe($updatedData['email'])
        ->and($contact->phone)->toBe($updatedData['phone'])
        ->and($contact->status->value)->toBe($updatedData['status'])
        ->and($contact->account_id)->toBe($account->id);
});

test('managers can edit any contact', function () {
    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $contact = Contact::factory()->create();
    $account = Account::factory()->create();

    $updatedData = [
        'first_name' => 'first name got updated',
        'last_name' => 'last name got updated',
        'email' => 'updated.john@example.cc',
        'phone' => 'OPL-CSD-444',
        'status' => ContactStatus::Client->value,
        'account_id' => $account->id,
    ];

    $this->actingAs($manager)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), $updatedData)
        ->assertRedirectToRoute('contacts.index')
        ->assertSessionHas('success', 'The contact #'.$contact->id.' has been updated.');

    $this->assertDatabaseCount('contacts', 1);

    $contact->refresh();

    expect($contact->user_id)->not()->toBe($manager->id)
        ->and($contact->first_name)->toBe($updatedData['first_name'])
        ->and($contact->last_name)->toBe($updatedData['last_name'])
        ->and($contact->email)->toBe($updatedData['email'])
        ->and($contact->phone)->toBe($updatedData['phone'])
        ->and($contact->status->value)->toBe($updatedData['status'])
        ->and($contact->account_id)->toBe($account->id);
});

it("returns forbidden when a sales agent tries to update another user's contact", function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create();

    $updatedData = [
        'first_name' => 'first name got updated',
        'last_name' => 'last name got updated',
        'email' => 'updated.john@example.cc',
        'phone' => 'OPL-CSD-444',
        'status' => ContactStatus::Client->value,
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), $updatedData)
        ->assertForbidden();

    $this->assertDatabaseCount('contacts', 1);

    $accountId = $contact->account_id;

    $contact->refresh();

    expect($contact->user_id)->not()->toBe($salesAgent->id)
        ->and($contact->first_name)->not()->toBe($updatedData['first_name'])
        ->and($contact->last_name)->not()->toBe($updatedData['last_name'])
        ->and($contact->email)->not()->toBe($updatedData['email'])
        ->and($contact->phone)->not()->toBe($updatedData['phone'])
        ->and($contact->status->value)->not()->toBe($updatedData['status'])
        ->and($contact->account_id)->toBe($accountId);
});

test('sales agents can edit their contacts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $updatedData = [
        'first_name' => 'Xxx',
        'last_name' => 'Yyy',
        'email' => 'xxx.yyy@example.cc',
        'phone' => '123-756-864',
        'status' => ContactStatus::Client->value,
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), $updatedData)
        ->assertRedirectToRoute('contacts.index')
        ->assertSessionHas('success', 'The contact #'.$contact->id.' has been updated.');

    $this->assertDatabaseCount('contacts', 1);

    $accountId = $contact->account_id;

    $contact->refresh();

    expect($contact->user_id)->toBe($salesAgent->id)
        ->and($contact->first_name)->toBe($updatedData['first_name'])
        ->and($contact->last_name)->toBe($updatedData['last_name'])
        ->and($contact->email)->toBe($updatedData['email'])
        ->and($contact->phone)->toBe($updatedData['phone'])
        ->and($contact->status->value)->toBe($updatedData['status'])
        ->and($contact->account_id)->toBe($accountId);
});

test('sales agents can assign their accounts to their contacts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $account = Account::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $updatedData = [
        'first_name' => 'Xxx',
        'last_name' => 'Yyy',
        'email' => 'xxx.yyy@example.cc',
        'phone' => '123-756-864',
        'status' => ContactStatus::Client->value,
        'account_id' => $account->id,
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), $updatedData)
        ->assertRedirectToRoute('contacts.index')
        ->assertSessionHas('success', 'The contact #'.$contact->id.' has been updated.');

    $this->assertDatabaseCount('contacts', 1);

    $contact->refresh();

    expect($contact->user_id)->toBe($salesAgent->id)
        ->and($contact->first_name)->toBe($updatedData['first_name'])
        ->and($contact->last_name)->toBe($updatedData['last_name'])
        ->and($contact->email)->toBe($updatedData['email'])
        ->and($contact->phone)->toBe($updatedData['phone'])
        ->and($contact->status->value)->toBe($updatedData['status'])
        ->and($contact->account_id)->toBe($account->id);
});

test('sales agents cannot assign accounts they do not own to their contacts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $account = Account::factory()->create();

    $updatedData = [
        'first_name' => 'Xxx',
        'last_name' => 'Yyy',
        'email' => 'xxx.yyy@example.cc',
        'phone' => '123-756-864',
        'status' => ContactStatus::Client->value,
        'account_id' => $account->id,
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), $updatedData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'account_id' => 'You do not have permission to assign this account.'
        ]);

    $this->assertDatabaseCount('contacts', 1);

    $contact->refresh();

    expect($contact->account_id)->toBeNull();
});

it('fails when using an already taken email', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $contacts = Contact::factory(2)->create();

    $updatedData = [
        'first_name' => 'Xxx',
        'last_name' => 'Yyy',
        'email' => $contacts[1]->email,
        'phone' => '123-756-864',
        'status' => ContactStatus::Client->value,
    ];

    $this->actingAs($admin)
        ->fromRoute('contacts.edit', $contacts[0])
        ->patch(route('contacts.update', $contacts[0]), $updatedData)
        ->assertRedirectToRoute('contacts.edit', $contacts[0])
        ->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
        ]);

    $this->assertDatabaseCount('contacts', 2);

    $accountId = $contacts[0]->account_id;

    $contacts[0]->refresh();

    expect($contacts[0]->user_id)->not()->toBe($admin->id)
        ->and($contacts[0]->first_name)->not()->toBe($updatedData['first_name'])
        ->and($contacts[0]->last_name)->not()->toBe($updatedData['last_name'])
        ->and($contacts[0]->email)->not()->toBe($updatedData['email'])
        ->and($contacts[0]->phone)->not()->toBe($updatedData['phone'])
        ->and($contacts[0]->status->value)->not()->toBe($updatedData['status'])
        ->and($contacts[0]->account_id)->toBe($accountId);
});

it('email can be nullable', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $contact = Contact::factory()->create();

    $updatedData = [
        'first_name' => 'Xxx',
        'last_name' => 'Yyy',
        'email' => null,
        'phone' => '123-756-864',
        'status' => ContactStatus::Client->value,
    ];

    $this->actingAs($admin)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), $updatedData)
        ->assertRedirectToRoute('contacts.index');

    $this->assertDatabaseCount('contacts', 1);

    $contact->refresh();

    expect($contact->email)->toBeNull();
});

it('fails with invalid first name', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), [
            'first_name' => $invalidValue,
            'last_name' => 'Moralis',
            'phone' => '341-888-344',
            'email' => 'moralis@test.com',
            'status' => ContactStatus::Client->value,
        ])
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'first_name' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('contacts', 1);
})->with('invalid-contact-first-name');

it('fails with invalid last name', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), [
            'first_name' => 'Maria',
            'last_name' => $invalidValue,
            'phone' => '341-888-344',
            'email' => 'moralis@test.com',
            'status' => ContactStatus::Client->value,
        ])
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'last_name' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('contacts', 1);
})->with('invalid-contact-last-name');

it('fails with invalid phone number', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), [
            'first_name' => 'Maria',
            'last_name' => 'Doker',
            'phone' => $invalidValue,
            'email' => 'moralis@test.com',
            'status' => ContactStatus::Client->value,
        ])
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'phone' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('contacts', 1);
})->with('invalid-contact-phone');

it('fails with invalid email', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), [
            'first_name' => 'Maria',
            'last_name' => 'Doker',
            'phone' => '1234-1234-1234',
            'email' => $invalidValue,
            'status' => ContactStatus::Client->value,
        ])
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'email' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('contacts', 1);
})->with('invalid-contact-email');

it('fails with invalid status', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), [
            'first_name' => 'Maria',
            'last_name' => 'Doker',
            'phone' => '1234-1234-1234',
            'email' => 'cool@test.cc',
            'status' => $invalidValue,
        ])
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'status' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('contacts', 1);
})->with('invalid-contact-status');

test('update contacts is rate limited', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    exhaustRateLimit('contacts-manage', (string) $salesAgent->id, 10);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.edit', $contact)
        ->patch(route('contacts.update', $contact), [
            'first_name' => 'Maria',
            'last_name' => 'Doker',
            'phone' => '1234-1234-1234',
            'email' => 'cool@test.cc',
            'status' => ContactStatus::Prospect->value,
        ])
        ->assertTooManyRequests();

    $this->assertDatabaseCount('contacts', 1);
})->with('invalid-contact-status');

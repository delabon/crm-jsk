<?php

declare(strict_types=1);

use App\Enums\ContactStatus;
use App\Enums\UserRole;
use App\Models\Account;
use App\Models\Contact;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('redirects non-authenticated users to the login page', function () {
    $this->get(route('contacts.create'))
        ->assertRedirectToRoute('login');
    $this->get(route('contacts.store'))
        ->assertRedirectToRoute('login');
});

it('redirects to email verification page when email is not verified', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('contacts.create'))
        ->assertRedirectToRoute('verification.notice');
    $this->actingAs($user)
        ->get(route('contacts.store'))
        ->assertRedirectToRoute('verification.notice');
});

it('returns forbidden when user does not have the required permissions', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('contacts.create'))
        ->assertForbidden();
    $this->actingAs($user)
        ->get(route('contacts.store'))
        ->assertForbidden();
});

it('renders the create contact page successfully', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->get(route('contacts.create'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page): void {
            $page->component('contacts/create');
        });
});

test('users with permissions can create contacts ', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contactData = [
        'first_name' => 'Joe',
        'last_name' => 'Doe',
        'phone' => '123-432-1234',
        'email' => 'joe.doe@test.test',
        'status' => ContactStatus::Prospect->value,
    ];

    $this->actingAs($salesAgent)
        ->post(route('contacts.store'), $contactData)
        ->assertRedirectToRoute('contacts.index')
        ->assertSessionHas('success', 'The contact has been created.');

    $this->assertDatabaseCount('contacts', 1);

    $contact = Contact::first();

    expect($contact->first_name)->toBe($contactData['first_name'])
        ->and($contact->last_name)->toBe($contactData['last_name'])
        ->and($contact->phone)->toBe($contactData['phone'])
        ->and($contact->email)->toBe($contactData['email'])
        ->and($contact->status)->toBe(ContactStatus::Prospect)
        ->and($contact->user_id)->toBe($salesAgent->id);
});

test('sales agents can assign their accounts to their contacts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $account = Account::factory()->create([
        'user_id' => $salesAgent->id,
    ]);

    $contactData = [
        'first_name' => 'Joe',
        'last_name' => 'Doe',
        'phone' => '123-432-1234',
        'email' => 'joe.doe@test.test',
        'status' => ContactStatus::Prospect->value,
        'account_id' => $account->id,
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.create')
        ->post(route('contacts.store'), $contactData)
        ->assertRedirectToRoute('contacts.index');

    $this->assertDatabaseCount('contacts', 1);

    expect(Contact::first()->user_id)->toBe($salesAgent->id);
});

test('sales agents cannot assign accounts they do not own to their contacts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $account = Account::factory()->create();

    $contactData = [
        'first_name' => 'Joe',
        'last_name' => 'Doe',
        'phone' => '123-432-1234',
        'email' => 'joe.doe@test.test',
        'status' => ContactStatus::Prospect->value,
        'account_id' => $account->id,
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.create')
        ->post(route('contacts.store'), $contactData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'account_id' => 'You do not have permission to assign this account.'
        ]);

    $this->assertDatabaseCount('contacts', 0);
});

test('email can be nullable', function () {
    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $contactData = [
        'first_name' => 'Dina',
        'last_name' => 'Moralis',
        'phone' => '341-888-344',
        'email' => null,
        'status' => ContactStatus::Client->value,
    ];

    $this->actingAs($manager)
        ->post(route('contacts.store'), $contactData)
        ->assertRedirectToRoute('contacts.index')
        ->assertSessionHas('success', 'The contact has been created.');

    $this->assertDatabaseCount('contacts', 1);

    $contact = Contact::first();

    expect($contact->first_name)->toBe($contactData['first_name'])
        ->and($contact->last_name)->toBe($contactData['last_name'])
        ->and($contact->phone)->toBe($contactData['phone'])
        ->and($contact->email)->toBeNull()
        ->and($contact->status)->toBe(ContactStatus::Client)
        ->and($contact->user_id)->toBe($manager->id);
});

test('email must be unique', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $contact = Contact::factory()->create([
        'email' => 'maria@test.com',
    ]);

    $contactData = [
        'first_name' => 'Dina',
        'last_name' => 'Moralis',
        'phone' => '341-888-344',
        'email' => $contact->email,
        'status' => ContactStatus::Client->value,
    ];

    $this->actingAs($admin)
        ->post(route('contacts.store'), $contactData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'email' => 'The email has already been taken.',
        ]);

    $this->assertDatabaseCount('contacts', 1);
});

it('fails with invalid first name', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.create')
        ->post(route('contacts.store'), [
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

    $this->assertDatabaseCount('contacts', 0);
})->with('invalid-contact-first-name');

it('fails with invalid last name', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.create')
        ->post(route('contacts.store'), [
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

    $this->assertDatabaseCount('contacts', 0);
})->with('invalid-contact-last-name');

it('fails with invalid phone number', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.create')
        ->post(route('contacts.store'), [
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

    $this->assertDatabaseCount('contacts', 0);
})->with('invalid-contact-phone');

it('fails with invalid email', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.create')
        ->post(route('contacts.store'), [
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

    $this->assertDatabaseCount('contacts', 0);
})->with('invalid-contact-email');

it('fails with invalid status', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('contacts.create')
        ->post(route('contacts.store'), [
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

    $this->assertDatabaseCount('contacts', 0);
})->with('invalid-contact-status');

test('create users is rate limited', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    exhaustRateLimit('contacts-manage', (string) $salesAgent->id, 10);

    $contactData = [
        'first_name' => 'Joe',
        'last_name' => 'Doe',
        'phone' => '123-432-1234',
        'email' => 'joe.doe@test.test',
        'status' => ContactStatus::Prospect->value,
    ];

    $this->actingAs($salesAgent)
        ->post(route('contacts.store'), $contactData)
        ->assertTooManyRequests();

    $this->assertDatabaseCount('contacts', 0);
});

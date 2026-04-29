<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Contact;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('redirects to login page when not authenticated', function () {
    $this->get(route('contacts.show', 123))
        ->assertRedirectToRoute('login');
});

it('redirects to email verification page when email is not verified', function () {
    $user = User::factory()->unverified()->create();

    $contact = Contact::factory()->create([
        'user_id' => $user->id
    ]);

    $this->actingAs($user)
        ->get(route('contacts.show', $contact))
        ->assertRedirectToRoute('verification.notice');
});

it('returns a not found response when contact does not exist', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('contacts.show', 9876543))
        ->assertNotFound();
});

it('returns a forbidden response when missing required permissions', function () {
    $user = User::factory()->create();

    $contact = Contact::factory()->create([
        'user_id' => $user->id
    ]);

    $this->actingAs($user)
        ->get(route('contacts.show', $contact))
        ->assertForbidden();
});

it('renders the contact show page successfully', function () {
    $user = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $contact = Contact::factory()->create([
        'user_id' => $user->id
    ]);

    $this->actingAs($user)
        ->get(route('contacts.show', $contact))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('contacts/show')
                ->has('contact')
                ->has('can')
                ->has('can.update')
                ->has('can.delete');
        });
});

test('admins and managers can see all contacts', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $contacts = Contact::factory(2)->create();

    $this->actingAs($admin)
        ->get(route('contacts.show', $contacts[0]))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('contacts.show', $contacts[1]))
        ->assertOk();

    $this->actingAs($manager)
        ->get(route('contacts.show', $contacts[0]))
        ->assertOk();

    $this->actingAs($manager)
        ->get(route('contacts.show', $contacts[1]))
        ->assertOk();
});

it("returns a forbidden response when a sales agent is trying to access a contact that they don't own", function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create();

    $this->actingAs($salesAgent)
        ->get(route('contacts.show', $contact))
        ->assertForbidden();
});

test("sales agent can only see their contacts", function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contact = Contact::factory()->create([
        'user_id' => $salesAgent->id
    ]);

    $this->actingAs($salesAgent)
        ->get(route('contacts.show', $contact))
        ->assertOk();
});

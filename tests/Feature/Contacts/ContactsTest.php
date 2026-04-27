<?php

// todo: pagination tests for users, contacts, accounts

declare(strict_types=1);

use App\Enums\ContactStatus;
use App\Enums\UserRole;
use App\Models\Contact;
use App\Models\User;
use Inertia\Testing\AssertableInertia;
use Illuminate\Support\Facades\Config;

it('redirects to login page when not authenticated', function () {
    $this->get(route('contacts.index'))
        ->assertRedirectToRoute('login');
});

it("redirects to email validation page when user's email is not verified", function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('contacts.index'))
        ->assertRedirectToRoute('verification.notice');
});

it("returns forbidden when user doesn't have required permissions", function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('contacts.index'))
        ->assertForbidden();
});

it('displays empty view when no contacts', function () {
    $user = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $this->actingAs($user)
        ->get(route('contacts.index'))
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('contacts/index')
                ->has('collection.data', 0);
        });
});

test('admins and managers can see all contacts', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $contacts = Contact::factory(3)->create();

    $contacts[0]->update([
        'user_id' => $admin->id,
    ]);

    $contacts[0]->update([
        'user_id' => $manager->id,
    ]);

    $this->actingAs($admin)
        ->get(route('contacts.index'))
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('contacts/index')
                ->has('collection.data', 3);
        });

    $this->actingAs($manager)
        ->get(route('contacts.index'))
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('contacts/index')
                ->has('collection.data', 3);
        });
});

test('sales agents can see only their contacts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contacts = Contact::factory(3)->create();

    $contacts[0]->update([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->get(route('contacts.index'))
        ->assertInertia(static function (AssertableInertia $page) use ($contacts) {
            $page->component('contacts/index')
                ->has('collection.data', 1)
                ->where('collection.data.0.id', $contacts[0]->id);
        });
});

test('admins and managers can search all contacts', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $contacts = Contact::factory(3)->create([
        'first_name' => 'search query'
    ]);

    $contacts[0]->update([
        'user_id' => $admin->id,
    ]);

    $contacts[0]->update([
        'user_id' => $manager->id,
    ]);

    $this->actingAs($admin)
        ->get(route('contacts.index', [
            'search' => 'search query'
        ]))
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('contacts/index')
                ->has('collection.data', 3);
        });

    $this->actingAs($manager)
        ->get(route('contacts.index', [
            'search' => 'search query'
        ]))
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('contacts/index')
                ->has('collection.data', 3);
        });
});

test('sales agent can search only their contacts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contacts = Contact::factory(3)->create([
        'first_name' => 'search query'
    ]);

    $contacts[0]->update([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->get(route('contacts.index', [
            'search' => 'search query'
        ]))
        ->assertInertia(static function (AssertableInertia $page) use ($contacts) {
            $page->component('contacts/index')
                ->has('collection.data', 1)
                ->where('collection.data.0.id', $contacts[0]->id);
        });
});

test('admins and managers can filter contacts by status', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $contacts = Contact::factory(3)->create([
        'status' => ContactStatus::Client,
    ]);

    $contacts[0]->update([
        'user_id' => $admin->id,
    ]);

    $contacts[0]->update([
        'user_id' => $manager->id,
    ]);

    $this->actingAs($admin)
        ->get(route('contacts.index', [
            'status' => ContactStatus::Client->value
        ]))
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('contacts/index')
                ->has('collection.data', 3);
        });

    $this->actingAs($manager)
        ->get(route('contacts.index', [
            'status' => ContactStatus::Client->value
        ]))
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('contacts/index')
                ->has('collection.data', 3);
        });
});

test('sales agent can filter only their contacts by status', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $contacts = Contact::factory(3)->create([
        'status' => ContactStatus::Prospect,
    ]);

    $contacts[1]->update([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->get(route('contacts.index', [
            'status' => ContactStatus::Prospect->value
        ]))
        ->assertInertia(static function (AssertableInertia $page) use ($contacts) {
            $page->component('contacts/index')
                ->has('collection.data', 1)
                ->where('collection.data.0.id', $contacts[1]->id);
        });
});

test('pagination', function () {
    Config::set('app.dashboard.per_page', 2);

    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $contacts = Contact::factory(3)->create();

    $this->actingAs($admin)
        ->get(route('contacts.index'))
        ->assertInertia(static function (AssertableInertia $page) use ($contacts) {
            $page->component('contacts/index')
                ->has('collection.data', 2)
                ->where('collection.data.0.id', $contacts[2]->id)
                ->where('collection.data.1.id', $contacts[1]->id);
        });

    $this->actingAs($admin)
        ->get(route('contacts.index', [
            'page' => 2,
        ]))
        ->assertInertia(static function (AssertableInertia $page) use ($contacts) {
            $page->component('contacts/index')
                ->has('collection.data', 1)
                ->where('collection.data.0.id', $contacts[0]->id);
        });
});

<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\Account;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('redirects to login page when not authenticated', function () {
    $this->get(route('accounts.index'))
        ->assertRedirectToRoute('login');
});

it("returns a forbidden response when user doesn't have required permissions", function () {
    $user = User::factory()
        ->create();

    $this->actingAs($user)
        ->get(route('accounts.index'))
        ->assertForbidden();
});

test('users with right permissions can visit the accounts page', function () {
    $user = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $this->actingAs($user)
        ->get(route('accounts.index'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('accounts/index');
        });
});

test('super admins and managers can view all accounts', function () {
    Account::factory(3)->create();
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $manager = User::factory()
        ->create()
        ->syncRoles([Role::Manager->value]);

    $this->actingAs($admin)
        ->get(route('accounts.index'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('accounts/index')
                ->has('collection.data', 3);
        });

    $this->actingAs($manager)
        ->get(route('accounts.index'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('accounts/index')
                ->has('collection.data', 3);
        });
});

test('sales agents can view only accounts they created', function () {
    $accounts = Account::factory(3)->create();
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);
    $accounts[0]->update([
        'user_id' => $salesAgent->id,
    ]);

    $this->actingAs($salesAgent)
        ->get(route('accounts.index'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($accounts) {
            $page->component('accounts/index')
                ->has('collection.data', 1)
                ->where('collection.data.0.id', $accounts[0]->id);
        });
});

test('sales agents can search only their accounts', function () {
    $searchTerm = 'noquerylike';
    $accounts = Account::factory(3)->create();
    $accounts[0]->update([
        'name' => $searchTerm,
    ]);
    $accounts[1]->update([
        'industry' => $searchTerm,
    ]);

    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $manager = User::factory()
        ->create()
        ->syncRoles([Role::Manager->value]);

    $this->actingAs($admin)
        ->get(route('accounts.index', ['search' => $searchTerm]))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($accounts) {
            $page->component('accounts/index')
                ->has('collection.data', 2)
                ->where('collection.data.0.id', $accounts[1]->id)
                ->where('collection.data.1.id', $accounts[0]->id);
        });

    $this->actingAs($manager)
        ->get(route('accounts.index', ['search' => $searchTerm]))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($accounts) {
            $page->component('accounts/index')
                ->has('collection.data', 2)
                ->where('collection.data.0.id', $accounts[1]->id)
                ->where('collection.data.1.id', $accounts[0]->id);
        });
});

test('super admins and managers can search all accounts', function () {
    $searchTerm = 'noquerylike';
    $accounts = Account::factory(3)->create();
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $manager = User::factory()
        ->create()
        ->syncRoles([Role::Manager->value]);
    $accounts[0]->update([
        'name' => $searchTerm,
    ]);
    $accounts[0]->update([
        'name' => $searchTerm,
        'user_id' => $admin->id,
    ]);
    $accounts[1]->update([
        'name' => $searchTerm,
        'user_id' => $manager->id,
    ]);

    $this->actingAs($admin)
        ->get(route('accounts.index', ['search' => $searchTerm]))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($accounts) {
            $page->component('accounts/index')
                ->has('collection.data', 2)
                ->where('collection.data.0.id', $accounts[1]->id)
                ->where('collection.data.1.id', $accounts[0]->id);
        });

    $this->actingAs($manager)
        ->get(route('accounts.index', ['search' => $searchTerm]))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($accounts) {
            $page->component('accounts/index')
                ->has('collection.data', 2)
                ->where('collection.data.0.id', $accounts[1]->id)
                ->where('collection.data.1.id', $accounts[0]->id);
        });
});

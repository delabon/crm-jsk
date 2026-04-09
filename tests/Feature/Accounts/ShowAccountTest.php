<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\Account;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('redirects to login page when not authenticated', function () {
    $account = Account::factory()->create();

    $this->get(route('accounts.show', $account))
        ->assertRedirectToRoute('login');
});

it("returns a forbidden response when user doesn't have required permissions", function () {
    $user = User::factory()->create();
    $account = Account::factory()->create();

    $this->actingAs($user)
        ->get(route('accounts.show', $account))
        ->assertForbidden();
});

test('super admin can view any account show page', function () {
    $admin = User::factory()->create()->syncRoles([Role::SuperAdmin->value]);
    $account = Account::factory()->create();

    $this->actingAs($admin)
        ->get(route('accounts.show', $account))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($account) {
            $page->component('accounts/show')
                ->where('account.id', $account->id)
                ->where('account.name', $account->name)
                ->has('account.owner_detail')
                ->has('can')
                ->where('can.update', true)
                ->where('can.delete', true);
        });
});

test('manager can view any account show page', function () {
    $manager = User::factory()->create()->syncRoles([Role::Manager->value]);
    $account = Account::factory()->create();

    $this->actingAs($manager)
        ->get(route('accounts.show', $account))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($account) {
            $page->component('accounts/show')
                ->where('account.id', $account->id)
                ->has('account.owner_detail');
        });
});

test('sales agent can view their own account show page', function () {
    $agent = User::factory()->create()->syncRoles([Role::SalesAgent->value]);
    $account = Account::factory()->create(['user_id' => $agent->id]);

    $this->actingAs($agent)
        ->get(route('accounts.show', $account))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($account) {
            $page->component('accounts/show')
                ->where('account.id', $account->id)
                ->has('account.owner_detail')
                ->where('can.update', true)
                ->where('can.delete', false);
        });
});

test('sales agent cannot view another agents account show page', function () {
    $agent1 = User::factory()->create()->syncRoles([Role::SalesAgent->value]);
    $agent2 = User::factory()->create()->syncRoles([Role::SalesAgent->value]);
    $account = Account::factory()->create(['user_id' => $agent2->id]);

    $this->actingAs($agent1)
        ->get(route('accounts.show', $account))
        ->assertForbidden();
});

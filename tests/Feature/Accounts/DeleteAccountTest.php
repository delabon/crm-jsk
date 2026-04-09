<?php

use App\Enums\Role;
use App\Models\Account;
use App\Models\User;

it('redirects non-authenticated users to the login page', function () {
    $account = Account::factory()
        ->create();

    $this->delete(route('accounts.destroy', ['account' => $account]))
        ->assertRedirectToRoute('login');
});

it('returns a forbidden response when the authenticated user lacks the required permissions', function () {
    $user = User::factory()
        ->create();

    $account = Account::factory()
        ->create();

    $this->actingAs($user)
        ->delete(route('accounts.destroy', ['account' => $account]))
        ->assertForbidden();
});

test('admins can delete their accounts and others', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $accounts = Account::factory(2)
        ->create();

    $accounts[0]->update([
        'user_id' => $admin->id,
    ]);

    expect($accounts[0]->user_id)->not()
        ->toBe($accounts[1]->user_id);

    $this->actingAs($admin);

    $this->fromRoute('accounts.index')
        ->delete(route('accounts.destroy', ['account' => $accounts[0]]))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$accounts[0]->id.' has been deleted.');

    $this->fromRoute('accounts.index')
        ->delete(route('accounts.destroy', ['account' => $accounts[1]]))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$accounts[1]->id.' has been deleted.');

    $this->assertDatabaseCount('accounts', 0);
});

test('managers can delete their accounts and others', function () {
    $manager = User::factory()
        ->create()
        ->syncRoles([Role::Manager->value]);

    $accounts = Account::factory(2)
        ->create();

    $accounts[0]->update([
        'user_id' => $manager->id,
    ]);

    expect($accounts[0]->user_id)->not()
        ->toBe($accounts[1]->user_id);

    $this->actingAs($manager);

    $this->fromRoute('accounts.index')
        ->delete(route('accounts.destroy', ['account' => $accounts[0]]))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$accounts[0]->id.' has been deleted.');

    $this->fromRoute('accounts.index')
        ->delete(route('accounts.destroy', ['account' => $accounts[1]]))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$accounts[1]->id.' has been deleted.');

    $this->assertDatabaseCount('accounts', 0);
});

test('sales agents cannot delete their accounts or others', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $accounts = Account::factory(2)
        ->create();

    $accounts[0]->update([
        'user_id' => $salesAgent->id,
    ]);

    expect($accounts[0]->user_id)->not()
        ->toBe($accounts[1]->user_id);

    $this->actingAs($salesAgent);

    $this->fromRoute('accounts.index')
        ->delete(route('accounts.destroy', ['account' => $accounts[0]]))
        ->assertForbidden();

    $this->fromRoute('accounts.index')
        ->delete(route('accounts.destroy', ['account' => $accounts[1]]))
        ->assertForbidden();

    $this->assertDatabaseCount('accounts', 2);
});

test('delete accounts is rate limited', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $account = Account::factory()
        ->create();

    exhaustRateLimit('accounts-manage', (string) $admin->id, 10);

    $this->actingAs($admin)
        ->fromRoute('accounts.index')
        ->delete(route('accounts.destroy', ['account' => $account]))
        ->assertTooManyRequests();

    $this->assertDatabaseCount('accounts', 1);
});

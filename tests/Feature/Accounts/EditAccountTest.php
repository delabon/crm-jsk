<?php

// Test: fails with invalid data
// Test: edit accounts is rate limited

use App\Enums\Role;
use App\Models\Account;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('redirects non-authenticated users to the login page', function () {
    $account = Account::factory()
        ->create();

    $this->get(route('accounts.edit', ['account' => $account]))
        ->assertRedirectToRoute('login');
    $this->get(route('accounts.update', ['account' => $account]))
        ->assertRedirectToRoute('login');
});

it('returns a forbidden response when the authenticated user lacks the required permissions', function () {
    $user = User::factory()
        ->create();

    $account = Account::factory()
        ->create();

    $this->actingAs($user)
        ->get(route('accounts.edit', ['account' => $account]))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('accounts.update', ['account' => $account]))
        ->assertForbidden();
});

it('renders the edit account page successfully', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $account = Account::factory()
        ->create();

    $this->actingAs($admin)
        ->get(route('accounts.edit', ['account' => $account]))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('accounts/edit');
        });
});

it('admins can edit their accounts and others', function () {
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

    $accountNewData = [
        'name' => 'Name updated by admin',
        'industry' => 'Name updated by admin',
        'website' => 'https://admin-updated.cc',
        'phone' => 'Phone updated by admin',
    ];

    // can edit their account
    $this->actingAs($admin)
        ->patch(route('accounts.update', ['account' => $accounts[0]]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$accounts[0]->id.' has been updated.');

    $accounts[0]->refresh();

    expect($accounts[0]->user_id)->toBe($admin->id)
        ->and($accounts[0]->name)->toBe($accountNewData['name'])
        ->and($accounts[0]->industry)->toBe($accountNewData['industry'])
        ->and($accounts[0]->website)->toBe($accountNewData['website'])
        ->and($accounts[0]->phone)->toBe($accountNewData['phone']);

    // can edit others accounts
    $this->actingAs($admin)
        ->patch(route('accounts.update', ['account' => $accounts[1]]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$accounts[1]->id.' has been updated.');

    $accounts[1]->refresh();

    expect($accounts[1]->user_id)->not()->toBe($admin->id)
        ->and($accounts[1]->name)->toBe($accountNewData['name'])
        ->and($accounts[1]->industry)->toBe($accountNewData['industry'])
        ->and($accounts[1]->website)->toBe($accountNewData['website'])
        ->and($accounts[1]->phone)->toBe($accountNewData['phone']);
});

it('managers can edit their accounts and others', function () {
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

    $accountNewData = [
        'name' => 'Name updated by manager',
        'industry' => 'Name updated by manager',
        'website' => 'https://manager-updated.cc',
        'phone' => 'Phone updated by manager',
    ];

    // can edit their account
    $this->actingAs($manager)
        ->patch(route('accounts.update', ['account' => $accounts[0]]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$accounts[0]->id.' has been updated.');

    $accounts[0]->refresh();

    expect($accounts[0]->user_id)->toBe($manager->id)
        ->and($accounts[0]->name)->toBe($accountNewData['name'])
        ->and($accounts[0]->industry)->toBe($accountNewData['industry'])
        ->and($accounts[0]->website)->toBe($accountNewData['website'])
        ->and($accounts[0]->phone)->toBe($accountNewData['phone']);

    // can edit others accounts
    $this->actingAs($manager)
        ->patch(route('accounts.update', ['account' => $accounts[1]]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$accounts[1]->id.' has been updated.');

    $accounts[1]->refresh();

    expect($accounts[1]->user_id)->not()->toBe($manager->id)
        ->and($accounts[1]->name)->toBe($accountNewData['name'])
        ->and($accounts[1]->industry)->toBe($accountNewData['industry'])
        ->and($accounts[1]->website)->toBe($accountNewData['website'])
        ->and($accounts[1]->phone)->toBe($accountNewData['phone']);
});

it('sales agents can edit their accounts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $account = Account::factory()
        ->create();

    $account->update([
        'user_id' => $salesAgent->id,
    ]);

    $accountNewData = [
        'name' => 'Name updated by sales agent',
        'industry' => 'Name updated by sales agent',
        'website' => 'https://sales-agent-updated.cc',
        'phone' => 'Phone updated by sales agent',
    ];

    $this->actingAs($salesAgent)
        ->patch(route('accounts.update', ['account' => $account]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The account #'.$account->id.' has been updated.');

    $account->refresh();

    expect($account->user_id)->toBe($salesAgent->id)
        ->and($account->name)->toBe($accountNewData['name'])
        ->and($account->industry)->toBe($accountNewData['industry'])
        ->and($account->website)->toBe($accountNewData['website'])
        ->and($account->phone)->toBe($accountNewData['phone']);
});

it('sales agents cannot edit others accounts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $account = Account::factory()
        ->create();

    $accountNewData = [
        'name' => 'Name updated by sales agent',
        'industry' => 'Name updated by sales agent',
        'website' => 'https://sales-agent-updated.cc',
        'phone' => 'Phone updated by sales agent',
    ];

    $this->actingAs($salesAgent)
        ->patch(route('accounts.update', ['account' => $account]), $accountNewData)
        ->assertForbidden();

    $account->refresh();

    expect($account->user_id)->not()->toBe($salesAgent->id)
        ->and($account->name)->not()->toBe($accountNewData['name'])
        ->and($account->industry)->not()->toBe($accountNewData['industry'])
        ->and($account->website)->not()->toBe($accountNewData['website'])
        ->and($account->phone)->not()->toBe($accountNewData['phone']);
});

it('fails with invalid name', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $account = Account::factory()
        ->create();

    $account->update([
        'user_id' => $salesAgent->id,
    ]);

    $accountNewData = [
        'name' => $invalidValue,
        'industry' => 'updated',
        'website' => 'https://updated.cc',
        'phone' => 'updated',
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.edit', ['account' => $account])
        ->patch(route('accounts.update', ['account' => $account]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'name' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('accounts', 1);

    expect($account->user_id)->toBe($salesAgent->id)
        ->and($account->name)->not()->toBe($accountNewData['name'])
        ->and($account->industry)->not()->toBe($accountNewData['industry'])
        ->and($account->website)->not()->toBe($accountNewData['website'])
        ->and($account->phone)->not()->toBe($accountNewData['phone']);
})->with('invalid-account-name');

it('fails with invalid industry', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $account = Account::factory()
        ->create();

    $account->update([
        'user_id' => $salesAgent->id,
    ]);

    $accountNewData = [
        'name' => 'Updated',
        'industry' => $invalidValue,
        'website' => 'https://updated.cc',
        'phone' => 'updated',
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.edit', ['account' => $account])
        ->patch(route('accounts.update', ['account' => $account]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'industry' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('accounts', 1);

    expect($account->user_id)->toBe($salesAgent->id)
        ->and($account->name)->not()->toBe($accountNewData['name'])
        ->and($account->industry)->not()->toBe($accountNewData['industry'])
        ->and($account->website)->not()->toBe($accountNewData['website'])
        ->and($account->phone)->not()->toBe($accountNewData['phone']);
})->with('invalid-account-industry');

it('fails with invalid website', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $account = Account::factory()
        ->create();

    $account->update([
        'user_id' => $salesAgent->id,
    ]);

    $accountNewData = [
        'name' => 'Updated',
        'industry' => 'Updated',
        'website' => $invalidValue,
        'phone' => 'updated',
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.edit', ['account' => $account])
        ->patch(route('accounts.update', ['account' => $account]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'website' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('accounts', 1);

    expect($account->user_id)->toBe($salesAgent->id)
        ->and($account->name)->not()->toBe($accountNewData['name'])
        ->and($account->industry)->not()->toBe($accountNewData['industry'])
        ->and($account->website)->not()->toBe($accountNewData['website'])
        ->and($account->phone)->not()->toBe($accountNewData['phone']);
})->with('invalid-account-website');

it('fails with invalid phone', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $account = Account::factory()
        ->create();

    $account->update([
        'user_id' => $salesAgent->id,
    ]);

    $accountNewData = [
        'name' => 'Updated',
        'industry' => 'Updated',
        'website' => 'https://updated.test',
        'phone' => $invalidValue,
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.edit', ['account' => $account])
        ->patch(route('accounts.update', ['account' => $account]), $accountNewData)
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'phone' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('accounts', 1);

    expect($account->user_id)->toBe($salesAgent->id)
        ->and($account->name)->not()->toBe($accountNewData['name'])
        ->and($account->industry)->not()->toBe($accountNewData['industry'])
        ->and($account->website)->not()->toBe($accountNewData['website'])
        ->and($account->phone)->not()->toBe($accountNewData['phone']);
})->with('invalid-account-phone');

test('edit accounts is rate limited', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);

    exhaustRateLimit('accounts-manage', (string) $admin->id, 10);

    $this->actingAs($admin)
        ->fromRoute('accounts.edit', ['account' => 123])
        ->patch(route('accounts.update', ['account' => 123], [
            'name' => 'AcmE CorP',
            'industry' => 'Finance',
            'website' => 'https://acme.test',
            'phone' => '123-1245-123',
        ]))
        ->assertTooManyRequests();

    $this->assertDatabaseCount('accounts', 0);
});

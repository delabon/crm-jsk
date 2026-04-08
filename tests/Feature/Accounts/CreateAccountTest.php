<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\Account;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

it('redirects non-authenticated users to the login page', function () {
    $this->get(route('accounts.create'))
        ->assertRedirectToRoute('login');
    $this->get(route('accounts.store'))
        ->assertRedirectToRoute('login');
});

it('returns a forbidden response when the authenticated user lacks the required permissions', function () {
    $user = User::factory()
        ->create();

    $this->actingAs($user)
        ->get(route('accounts.create'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('accounts.store'))
        ->assertForbidden();
});

it('renders the create account page successfully', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $this->actingAs($admin)
        ->get(route('accounts.create'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('accounts/create');
        });
});

test('an admin can create an account', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $newAccountData = [
        'name' => 'AcmE CorP',
        'description' => 'Some acme corp description',
        'industry' => 'Finance',
        'website' => 'https://acme.test',
        'phone' => '123-1245-123',
    ];

    $this->actingAs($admin)
        ->fromRoute('accounts.create')
        ->post(route('accounts.store', $newAccountData))
        ->assertRedirectToRoute('accounts.index')
        ->assertSessionHas('success', 'The account has been created.');

    $this->assertDatabaseCount('accounts', 1);

    $account = Account::first();

    expect($account->user_id)->toBe($admin->id)
        ->and($account->name)->toBe($newAccountData['name'])
        ->and($account->description)->toBe($newAccountData['description'])
        ->and($account->industry)->toBe($newAccountData['industry'])
        ->and($account->website)->toBe($newAccountData['website'])
        ->and($account->phone)->toBe($newAccountData['phone']);
});

test('a manager can create an account', function () {
    $manager = User::factory()
        ->create()
        ->syncRoles([Role::Manager->value]);

    $newAccountData = [
        'name' => fake()->company(),
        'industry' => 'Technology',
        'website' => 'https://superfake.cc',
        'phone' => '98 123 123',
    ];

    $this->actingAs($manager)
        ->fromRoute('accounts.create')
        ->post(route('accounts.store', $newAccountData))
        ->assertRedirectToRoute('accounts.index')
        ->assertSessionHas('success', 'The account has been created.');

    $this->assertDatabaseCount('accounts', 1);

    $account = Account::first();

    expect($account->user_id)->toBe($manager->id)
        ->and($account->name)->toBe($newAccountData['name'])
        ->and($account->industry)->toBe($newAccountData['industry'])
        ->and($account->website)->toBe($newAccountData['website'])
        ->and($account->phone)->toBe($newAccountData['phone']);
});

test('a sales agent can create an account', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $newAccountData = [
        'name' => fake()->company(),
        'industry' => 'Real Estate',
        'website' => 'https://real.cc',
        'phone' => '000 989 123',
    ];

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.create')
        ->post(route('accounts.store', $newAccountData))
        ->assertRedirectToRoute('accounts.index')
        ->assertSessionHas('success', 'The account has been created.');

    $this->assertDatabaseCount('accounts', 1);

    $account = Account::first();

    expect($account->user_id)->toBe($salesAgent->id)
        ->and($account->name)->toBe($newAccountData['name'])
        ->and($account->industry)->toBe($newAccountData['industry'])
        ->and($account->website)->toBe($newAccountData['website'])
        ->and($account->phone)->toBe($newAccountData['phone']);
});

it('fails with invalid name', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.create')
        ->post(route('accounts.store', [
            'name' => $invalidValue,
            'industry' => 'Real Estate',
            'website' => 'https://real.cc',
            'phone' => '000 989 123',
        ]))
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'name' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('accounts', 0);
})->with('invalid-account-name');

it('fails with invalid industry', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.create')
        ->post(route('accounts.store', [
            'name' => 'Valid Name',
            'industry' => $invalidValue,
            'website' => 'https://real.cc',
            'phone' => '000 989 123',
        ]))
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'industry' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('accounts', 0);
})->with('invalid-account-industry');

it('fails with invalid website', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.create')
        ->post(route('accounts.store', [
            'name' => 'Valid Name',
            'industry' => 'Finance',
            'website' => $invalidValue,
            'phone' => '000 989 123',
        ]))
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'website' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('accounts', 0);
})->with('invalid-account-website');

it('fails with invalid phone', function (mixed $invalidValue, string $expectedMessage) {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([Role::SalesAgent->value]);

    $this->actingAs($salesAgent)
        ->fromRoute('accounts.create')
        ->post(route('accounts.store', [
            'name' => 'Valid Name',
            'industry' => 'Finance',
            'website' => 'https://website.test',
            'phone' => $invalidValue,
        ]))
        ->assertRedirectBack()
        ->assertSessionHasErrors([
            'phone' => $expectedMessage,
        ]);

    $this->assertDatabaseCount('accounts', 0);
})->with('invalid-account-phone');

test('create accounts is rate limited', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([Role::SuperAdmin->value]);

    exhaustRateLimit('accounts-manage', (string) $admin->id, 10);

    $this->actingAs($admin)
        ->fromRoute('accounts.create')
        ->post(route('accounts.store', [
            'name' => 'AcmE CorP',
            'industry' => 'Finance',
            'website' => 'https://acme.test',
            'phone' => '123-1245-123',
        ]))
        ->assertTooManyRequests();

    $this->assertDatabaseCount('accounts', 0);
});

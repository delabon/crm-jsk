<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Config;

it('returns unauthorized when not authenticated', function () {
    $this->postJson(route('api.private.v1.accounts.search', ['search' => 'acme']))
        ->assertUnauthorized();
});

it('returns forbidden when the email is not verified', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->postJson(route('api.private.v1.accounts.search', ['search' => 'acme']))
        ->assertForbidden();
});

it('requires a search term', function () {
    $admin = User::factory()->create()
        ->syncRoles([
            UserRole::SuperAdmin->value,
        ]);

    $this->actingAs($admin)
        ->postJson(route('api.private.v1.accounts.search'))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['search']);
});

it('returns matching accounts for an admin', function () {
    $admin = User::factory()->create()
        ->syncRoles([
            UserRole::SuperAdmin->value,
        ]);
    $acme = Account::factory()->create([
        'name' => 'Acme Corporation',
        'user_id' => $admin->id,
    ]);
    Account::factory()->create(['name' => 'Globex']);

    $this->actingAs($admin)
        ->postJson(route('api.private.v1.accounts.search', ['search' => 'Acme']))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.value', $acme->id)
        ->assertJsonPath('0.label', 'Acme Corporation');
});

it('lets a manager see accounts they do not own', function () {
    $manager = User::factory()->create()->syncRoles([UserRole::Manager->value]);
    $owner = User::factory()->create()->syncRoles([UserRole::SalesAgent->value]);
    $acme = Account::factory()->create([
        'name' => 'Acme Holdings',
        'user_id' => $owner->id,
    ]);

    $this->actingAs($manager)
        ->postJson(route('api.private.v1.accounts.search', ['search' => 'Acme']))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.value', $acme->id);
});

it('scopes a sales agent to their own accounts only', function () {
    $agent = User::factory()->create()->syncRoles([UserRole::SalesAgent->value]);
    $otherAgent = User::factory()->create()->syncRoles([UserRole::SalesAgent->value]);
    $own = Account::factory()->create([
        'name' => 'Acme Alpha',
        'user_id' => $agent->id,
    ]);
    Account::factory()->create([
        'name' => 'Acme Beta',
        'user_id' => $otherAgent->id,
    ]);

    $this->actingAs($agent)
        ->postJson(route('api.private.v1.accounts.search', ['search' => 'Acme']))
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.value', $own->id)
        ->assertJsonPath('0.label', 'Acme Alpha');
});

it('returns only label and value for each account', function () {
    $admin = User::factory()->create()->syncRoles([UserRole::SuperAdmin->value]);
    Account::factory()->create(['name' => 'Acme']);

    $this->actingAs($admin)
        ->postJson(route('api.private.v1.accounts.search', ['search' => 'Acme']))
        ->assertOk()
        ->assertJsonStructure([
            ['label', 'value'],
        ]);
});

it('respects the autocomplete per page config', function () {
    Config::set('app.dashboard.account_autocomplete_per_page', 2);

    $admin = User::factory()->create()->syncRoles([UserRole::SuperAdmin->value]);
    Account::factory(3)
        ->sequence(
            ['name' => 'Acme One'],
            ['name' => 'Acme Two'],
            ['name' => 'Acme Three'],
        )
        ->create(['user_id' => $admin->id]);

    $this->actingAs($admin)
        ->postJson(route('api.private.v1.accounts.search', ['search' => 'Acme']))
        ->assertOk()
        ->assertJsonCount(2);
});

it('returns an empty array when nothing matches', function () {
    $admin = User::factory()->create()->syncRoles([UserRole::SuperAdmin->value]);
    Account::factory()->create(['name' => 'Globex']);

    $this->actingAs($admin)
        ->postJson(route('api.private.v1.accounts.search', ['search' => 'zzz-nope']))
        ->assertOk()
        ->assertJsonCount(0);
});

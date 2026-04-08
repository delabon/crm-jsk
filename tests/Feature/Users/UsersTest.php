<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('super admins can visit the users page', function () {
    $user = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $this->actingAs($user);

    $this->get(route('users.index'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('users/index');
        });
});

test('non super admins cannot visit the users page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.index'))
        ->assertForbidden();
});

test('users can be displayed', function () {
    $users = User::factory(3)->create();

    $users[0]->syncRoles([Role::SuperAdmin->value]);

    $this->actingAs($users[0])
        ->get(route('users.index'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($users) {
            $page->component('users/index')
                ->has('collection.data', 3)
                ->where('collection.data.0.first_name', $users[2]->first_name)
                ->where('collection.data.1.first_name', $users[1]->first_name)
                ->where('collection.data.2.first_name', $users[0]->first_name);
        });
});

test('users can be searched', function () {
    $users = User::factory(3)->create();

    $users[0]->syncRoles([Role::SuperAdmin->value]);

    $users[1]->update([
        'first_name' => 'unique',
    ]);

    $users[2]->update([
        'email' => 'unique@email.com',
    ]);

    $this->actingAs($users[0])
        ->get(route('users.index', ['search' => 'unique']))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($users) {
            $page->component('users/index')
                ->has('collection.data', 2)
                ->where('collection.data.0.first_name', $users[2]->first_name)
                ->where('collection.data.1.first_name', $users[1]->first_name);
        });
});

test('filter users by verified', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $user1 = User::factory()->unverified()->create();
    $user2 = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.index', ['verified' => 'yes']))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($user2) {
            $page->component('users/index')
                ->has('collection.data', 2)
                ->where('collection.data.0.first_name', $user2->first_name);
        });
});

test('filter users by non-verified', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $user1 = User::factory()->unverified()->create();
    $user2 = User::factory()->create();

    $this->actingAs($admin)
        ->get(route('users.index', ['verified' => 'no']))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($user1) {
            $page->component('users/index')
                ->has('collection.data', 1)
                ->where('collection.data.0.first_name', $user1->first_name);
        });
});

test('filter users by role', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $user1 = User::factory()->unverified()->create();
    $user2 = User::factory()->create()
        ->syncRoles([Role::Manager->value]);

    $this->actingAs($admin)
        ->get(route('users.index', ['role' => 'manager']))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) use ($user2) {
            $page->component('users/index')
                ->has('collection.data', 1)
                ->where('collection.data.0.first_name', $user2->first_name);
        });
});

<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;

test('non logged in users will be redirected to login page', function () {
    $this
        ->get(route('users.create'))
        ->assertRedirectToRoute('login');
    $this
        ->post(route('users.store'))
        ->assertRedirectToRoute('login');
});

test('super admins can visit the create user page', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $this->actingAs($admin);

    $this->get(route('users.create'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('users/store');
        });
});

test('non super admins cannot visit the create user page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.create'))
        ->assertForbidden();
});

test('super admins can create a verified user', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $this->actingAs($admin);

    $newUserData = [
        'first_name' => 'John',
        'last_name' => 'doe',
        'email' => 'john.doe@test.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
        'role' => Role::User->value,
    ];

    $this->post(route('users.store'), $newUserData)
        ->assertRedirectToRoute('users.index')
        ->assertSessionHas('success', 'The user has been created.');

    $this->assertDatabaseCount('users', 2);

    $user = User::query()->where('email', $newUserData['email'])->first();

    expect($user->id)->not()->toEqual($admin->id)
        ->and($user->first_name)->toEqual($newUserData['first_name'])
        ->and($user->last_name)->toEqual($newUserData['last_name'])
        ->and($user->main_role->value)->toEqual($newUserData['role'])
        ->and(Hash::check($newUserData['password'], $user->password))->toBeTrue();
});

test('non super admins cannot create users', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(route('users.store'))
        ->assertForbidden();
});

it('fails to create a new user when using invalid first name',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->syncRoles([Role::SuperAdmin->value]);

        $this->actingAs($admin);

        $this->post(route('users.store'), [
            'first_name' => $invalidValue,
            'last_name' => 'User',
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['first_name' => $expectedMessage]);

        $this->assertDatabaseCount('users', 1);
    }
)->with('invalid-user-first-name');

it('fails to create a new user when using invalid last name',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->syncRoles([Role::SuperAdmin->value]);

        $this->actingAs($admin);

        $this->post(route('users.store'), [
            'first_name' => 'Test',
            'last_name' => $invalidValue,
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['last_name' => $expectedMessage]);

        $this->assertDatabaseCount('users', 1);
    }
)->with('invalid-user-last-name');

it('fails to create a new user when using invalid email',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->syncRoles([Role::SuperAdmin->value]);

        $this->actingAs($admin);

        $this->post(route('users.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $invalidValue,
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['email' => $expectedMessage]);

        $this->assertDatabaseCount('users', 1);
    }
)->with('invalid-user-email');

it('fails to create a new user when using invalid password',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->syncRoles([Role::SuperAdmin->value]);

        $this->actingAs($admin);

        $this->post(route('users.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@test.cc',
            'role' => Role::User->value,
            'password' => $invalidValue,
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['password' => $expectedMessage]);

        $this->assertDatabaseCount('users', 1);
    }
)->with('invalid-user-password');

it('fails to create a new user when using invalid role',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->syncRoles([Role::SuperAdmin->value]);

        $this->actingAs($admin);

        $this->post(route('users.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@test.cc',
            'role' => $invalidValue,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['role' => $expectedMessage]);

        $this->assertDatabaseCount('users', 1);
    }
)->with('invalid-user-role');

test('create users is rate limited', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $this->actingAs($admin);

    exhaustRateLimit('users-manage', (string) $admin->id, maxAttempts: 10);

    $this->actingAs($admin)
        ->post(route('users.store'), [])
        ->assertTooManyRequests();
});

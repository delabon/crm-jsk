<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;

test('non logged in users will be redirected to login page', function () {
    $this
        ->get(route('users.edit', 23123))
        ->assertRedirectToRoute('login');
    $this
        ->patch(route('users.update', 23123))
        ->assertRedirectToRoute('login');
});

test('super admins can visit the edit user page', function () {
    $admin = User::factory()->create()
        ->removeRole(Role::User->value)
        ->assignRole(Role::SuperAdmin->value);
    $this->actingAs($admin);

    $this->get(route('users.edit', $admin))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('users/edit');
        });
});

test('non super admins cannot visit the edit user page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.edit', $user))
        ->assertForbidden();
});

test('super admins can edit a user', function () {
    $admin = User::factory()->create()
        ->removeRole(Role::User->value)
        ->assignRole(Role::SuperAdmin->value);
    $this->actingAs($admin);

    $user = User::factory()->create();

    $newUserData = [
        'first_name' => 'John',
        'last_name' => 'doe',
        'email' => 'john.doe@test.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
        'role' => Role::User->value,
    ];

    $this->patch(route('users.update', $user), $newUserData)
        ->assertRedirectToRoute('users.index')
        ->assertSessionHas('success', 'The user #'.$user->id.' has been updated.');

    $this->assertDatabaseCount('users', 2);

    $user = User::query()->where('email', $newUserData['email'])->first();

    expect($user->id)->not()->toEqual($admin->id)
        ->and($user->first_name)->toEqual($newUserData['first_name'])
        ->and($user->last_name)->toEqual($newUserData['last_name'])
        ->and($user->main_role->value)->toEqual($newUserData['role'])
        ->and(Hash::check($newUserData['password'], $user->password))->toBeTrue();
});

test('non super admins cannot edit users', function () {
    $this
        ->actingAs(User::factory()->create())
        ->patch(route('users.update', User::factory()->create()), [
            'first_name' => 'John',
            'last_name' => 'doe',
            'email' => 'john.doe@test.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'role' => Role::User->value,
        ])
        ->assertForbidden();
});

it('fails to edit a user when using invalid first name',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->removeRole(Role::User->value)
            ->assignRole(Role::SuperAdmin->value);
        $this->actingAs($admin);

        $user = User::factory()->create();

        $this->patch(route('users.update', $user), [
            'first_name' => $invalidValue,
            'last_name' => 'User',
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['first_name' => $expectedMessage]);

        $user->refresh();

        expect($user->first_name)->not()->toEqual($invalidValue);
    }
)->with('invalid-user-first-name');

it('fails to edit a user when using invalid last name',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->removeRole(Role::User->value)
            ->assignRole(Role::SuperAdmin->value);
        $this->actingAs($admin);

        $user = User::factory()->create();

        $this->patch(route('users.update', $user), [
            'first_name' => 'User',
            'last_name' => $invalidValue,
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['last_name' => $expectedMessage]);

        $user->refresh();

        expect($user->last_name)->not()->toEqual($invalidValue);
    }
)->with('invalid-user-last-name');

it('fails to edit a user when using invalid email',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->removeRole(Role::User->value)
            ->assignRole(Role::SuperAdmin->value);
        $this->actingAs($admin);

        $user = User::factory()->create();

        $this->patch(route('users.update', $user), [
            'first_name' => 'User',
            'last_name' => 'Test',
            'email' => $invalidValue,
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['email' => $expectedMessage]);

        $user->refresh();

        expect($user->email)->not()->toEqual($invalidValue);
    }
)->with('invalid-user-email');

it('fails to edit a user when using invalid role',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->removeRole(Role::User->value)
            ->assignRole(Role::SuperAdmin->value);
        $this->actingAs($admin);

        $user = User::factory()->create();

        $this->patch(route('users.update', $user), [
            'first_name' => 'User',
            'last_name' => 'Test',
            'email' => 'test@test.com',
            'role' => $invalidValue,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['role' => $expectedMessage]);

        $user->refresh();

        expect($user->main_role->value)->not()->toEqual($invalidValue);
    }
)->with('invalid-user-role');

it('fails to edit the password of the user when using invalid password',
    function (mixed $invalidValue, string $expectedMessage) {
        $admin = User::factory()->create()
            ->removeRole(Role::User->value)
            ->assignRole(Role::SuperAdmin->value);
        $this->actingAs($admin);

        $user = User::factory()->create();

        $this->put(route('user-password.update', $user), [
            'first_name' => 'User',
            'last_name' => 'Test',
            'email' => 'test@test.com',
            'role' => Role::User->value,
            'password' => $invalidValue,
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['password' => $expectedMessage]);

        $user->refresh();

        expect(Hash::check($invalidValue, $user->password))->toBeFalse();
    }
)->with('invalid-user-password');

test('edit users is rate limited', function () {
    $admin = User::factory()->create()
        ->removeRole(Role::User->value)
        ->assignRole(Role::SuperAdmin->value);
    $this->actingAs($admin);

    exhaustRateLimit('users-manage', (string) $admin->id, maxAttempts: 10);

    $this
        ->actingAs($admin)
        ->patch(route('users.update', 123), [])
        ->assertTooManyRequests();
});

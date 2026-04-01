<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('non logged in users will be redirected to login page', function () {
    $this
        ->post(route('users.store'))
        ->assertRedirectToRoute('login');
});

test('super admins can visit the create user page', function () {
    $admin = User::factory()->create()
        ->removeRole(Role::User->value)
        ->assignRole(Role::SuperAdmin->value);
    $this->actingAs($admin);

    $this->get(route('users.create'))
        ->assertOk();
});

test('non super admins cannot visit the create user page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.create'))
        ->assertForbidden();
});

test('super admins can create a verified user', function () {
    $admin = User::factory()->create()
        ->removeRole(Role::User->value)
        ->assignRole(Role::SuperAdmin->value);
    $this->actingAs($admin);

    $newUserDate = [
        'first_name' => 'John',
        'last_name' => 'doe',
        'email' => 'john.doe@test.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
        'role' => Role::User->value,
    ];

    $this->post(route('users.store',$newUserDate))
        ->assertRedirectToRoute('users.index')
        ->assertSessionHas('success', 'The user has been created.');

    $this->assertDatabaseCount('users', 2);

    $user = User::find(2);

    expect($user->id)->not()->toEqual($admin->id)
        ->and($user->email)->toEqual($newUserDate['email'])
        ->and($user->first_name)->toEqual($newUserDate['first_name'])
        ->and($user->last_name)->toEqual($newUserDate['last_name'])
        ->and($user->main_role->value)->toEqual($newUserDate['role'])
        ->and(Hash::check($newUserDate['password'], $user->password));
});

test('non super admins cannot create users', function () {
    $this
        ->actingAs(User::factory()->create())
        ->post(route('users.store'))
        ->assertForbidden();
});

it('fails to create a new user when using invalid user data',
    function (array $data, string $invalidParam, string $expectedMessage) {
        $admin = User::factory()->create()
            ->removeRole(Role::User->value)
            ->assignRole(Role::SuperAdmin->value);
        $this->actingAs($admin);

        // dd($data, $invalidParam, $expectedMessage);
        $this->post(route('users.store'), $data)
            ->assertRedirectBack()
            ->assertSessionHasErrors([$invalidParam => $expectedMessage]);

        $this->assertDatabaseCount('users', 1);
    }
)->with([
    // First Name
    [
        [
            'first_name' => null,
            'last_name' => 'User',
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'first_name',
        'The first name field is required.'
    ],
    [
        [
            'first_name' => '',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'first_name',
        'The first name field is required.'
    ],
    [
        [
            'first_name' => str_repeat('A', 256),
            'last_name' => 'User',
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'first_name',
        'The first name field must not be greater than 255 characters.'
    ],
    // Last Name
    [
        [
            'first_name' => 'Test',
            'last_name' => null,
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'last_name',
        'The last name field is required.'
    ],
    [
        [
            'first_name' => 'Test',
            'last_name' => '',
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'last_name',
        'The last name field is required.'
    ],
    [
        [
            'first_name' => 'Test',
            'last_name' => str_repeat('A', 256),
            'email' => 'test@example.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'last_name',
        'The last name field must not be greater than 255 characters.'
    ],
    // Email
    [
        [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => null,
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'email',
        'The email field is required.'
    ],
    [
        [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'some-invalid-email',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'email',
        'The email field must be a valid email address.'
    ],
    [
        [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => str_repeat('a', 255).'@cc.com',
            'role' => Role::User->value,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'email',
        'The email field must not be greater than 255 characters.'
    ],
    // Password
    [
        [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'valid@email.com',
            'role' => Role::User->value,
            'password' => null,
            'password_confirmation' => 'password',
        ],
        'password',
        'The password field is required.'
    ],
    [
        [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'valid@email.com',
            'role' => Role::User->value,
            'password' => '1',
            'password_confirmation' => 'password',
        ],
        'password',
        'The password field must be at least 8 characters.'
    ],
    [
        [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'valid@email.com',
            'role' => Role::User->value,
            'password' => '12345678',
            'password_confirmation' => 'password',
        ],
        'password',
        'The password field confirmation does not match.'
    ],
    // Role
    [
        [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'valid@email.com',
            'role' => null,
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'role',
        'The role field is required.'
    ],
    [
        [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'valid@email.com',
            'role' => 'non-existent role',
            'password' => 'password',
            'password_confirmation' => 'password',
        ],
        'role',
        'The selected role is invalid.'
    ],
]);

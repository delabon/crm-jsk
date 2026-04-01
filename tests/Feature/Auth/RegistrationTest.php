<?php

declare(strict_types=1);

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $this->assertDatabaseCount('users', 0);

    $response = $this->post(route('register.store'), [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertDatabaseCount('users', 1);
    $this->assertAuthenticated();
    $response->assertRedirect(route('verification.notice'));

    $user = User::first();

    $this->assertNull($user->email_verified_at);
});

it('fails to register with invalid user data',
    function (array $data, string $invalidParam, string $expectedMessage) {
    // dd($data, $invalidParam, $expectedMessage);
        $this->post(route('register.store'), $data)
            ->assertRedirectBack()
            ->assertSessionHasErrors([$invalidParam => $expectedMessage]);

        $this->assertDatabaseCount('users', 0);
    }
)->with([
    // First Name
    [
        [
            'first_name' => null,
            'last_name' => 'User',
            'email' => 'test@example.com',
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
            'password' => '12345678',
            'password_confirmation' => 'password',
        ],
        'password',
        'The password field confirmation does not match.'
    ],
]);

test('fails when trying to registered with an already existed email', function () {
    $user = User::factory()->create();

    $this->post(route('register.store'), [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => $user->email,
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertRedirectBack()
        ->assertSessionHasErrors(['email' => 'The email has already been taken.']);

    $this->assertDatabaseCount('users', 1);
});

test('user is redirected to dashboard when already logged in', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('register'));

    $response->assertRedirectToRoute('dashboard');
});

test('register is rate limited', function () {
    for ($i=0; $i<5; $i++) {
        $this->post(route('register.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test'.$i.'@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $this->assertAuthenticated();

        $this->delete(route('logout'));
    }

    $this->post(route('register.store'), [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test6@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])
        ->assertTooManyRequests();
    $this->assertGuest();

    $this->assertDatabaseCount('users', 5);
});

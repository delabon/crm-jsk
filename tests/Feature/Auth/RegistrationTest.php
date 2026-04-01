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

it('fails to register when using invalid first name',
    function (mixed $invalidValue, string $expectedMessage) {
        $this->post(route('register.store'), [
            'first_name' => $invalidValue,
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['first_name' => $expectedMessage]);

        $this->assertDatabaseCount('users', 0);
    }
)->with('invalid-user-first-name');

it('fails to register when using invalid last name',
    function (mixed $invalidValue, string $expectedMessage) {
        $this->post(route('register.store'), [
            'first_name' => 'Test',
            'last_name' => $invalidValue,
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['last_name' => $expectedMessage]);

        $this->assertDatabaseCount('users', 0);
    }
)->with('invalid-user-last-name');

it('fails to register when using invalid email',
    function (mixed $invalidValue, string $expectedMessage) {
        $this->post(route('register.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $invalidValue,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['email' => $expectedMessage]);

        $this->assertDatabaseCount('users', 0);
    }
)->with('invalid-user-email');

it('fails to register when using invalid password',
    function (mixed $invalidValue, string $expectedMessage) {
        $this->post(route('register.store'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test.user@test.cc',
            'password' => $invalidValue,
            'password_confirmation' => 'password',
        ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['password' => $expectedMessage]);

        $this->assertDatabaseCount('users', 0);
    }
)->with('invalid-user-password');

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

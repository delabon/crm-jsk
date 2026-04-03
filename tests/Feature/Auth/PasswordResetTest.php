<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia;

test('reset password link screen can be rendered', function () {
    $this->get(route('password.request'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('auth/forgot-password');
        });
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this->get(route('password.reset', [
            'token' => $notification->token,
            'email' => $user->email,
        ]))
            ->assertOk()
            ->assertInertia(static function (AssertableInertia $page) {
                $page->component('auth/reset-password');
            });

        return true;
    });
});

test('password can be updated with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post(route('password.email'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post(route('password.update'), [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

test('password cannot be updated with invalid token', function () {
    $user = User::factory()->create();
    $invalidToken = 'invalid-token';

    $this->post(route('password.update'), [
        'token' => $invalidToken,
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])
        ->assertRedirectToRoute('password.reset', ['token' => $invalidToken])
        ->assertSessionHasErrors('email');
});

test('send password email is rate limited', function () {
    $user = User::factory()->create();

    exhaustRateLimit('password-email', '127.0.0.1', maxAttempts: 5);

    $this->post(route('password.email'), ['email' => $user->email])
        ->assertTooManyRequests();
});

test('update password is rate limited', function () {
    $user = User::factory()->create();

    exhaustRateLimit('password-update', '127.0.0.1', maxAttempts: 5);

    $this->post(route('password.update'), [
        'token' => 'valid-token',
        'email' => $user->email,
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])
        ->assertTooManyRequests();
});

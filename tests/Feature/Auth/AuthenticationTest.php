<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('login screen can be rendered', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('auth/login');
        });
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();

    $response = $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->delete(route('logout'));

    $this->assertGuest();
    $response->assertRedirect(route('login'));
});

test('login is rate limited', function () {
    $user = User::factory()->create();

    for ($i=0; $i<5; $i++) {
        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $this->assertAuthenticated();

        $this->delete(route('logout'));
        $this->assertGuest();
    }

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertTooManyRequests();
    $this->assertGuest();
});

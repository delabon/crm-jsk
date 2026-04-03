<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('settings/profile');
        });
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();

    expect($user->first_name)->toBe('Test')
        ->and($user->last_name)->toBe('User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->email_verified_at)->toBeNull();
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->patch(route('profile.update'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => $user->email,
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    expect($user->refresh()->email_verified_at)->not->toBeNull();
});

test('password can be updated', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->put(route('user-password.update', $user), [
            'current_password' => 'password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirectBack();

    expect(Hash::check('new-password', $user->refresh()->password))->toBeTrue();
});

it('fails with invalid password',
    function (mixed $invalidPassword, string $expectedMessage) {
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->put(route('user-password.update', $user), [
                'current_password' => 'password',
                'password' => $invalidPassword,
                'password_confirmation' => 'new-password',
            ])
            ->assertRedirectBack()
            ->assertSessionHasErrors(['password' => $expectedMessage]);
    }
)->with('invalid-user-password');

test('user can delete their account', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->delete(route('profile.destroy'), [
            'password' => 'password',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('home'));

    $this->assertGuest();
    expect($user->fresh())->toBeNull();
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->from(route('profile.edit'))
        ->delete(route('profile.destroy'), [
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrors('password')
        ->assertRedirect(route('profile.edit'));

    expect($user->fresh())->not->toBeNull();
});

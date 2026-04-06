<?php

declare(strict_types=1);

use App\Enums\Role;
use App\Models\User;

test('non logged in users will be redirected to login page', function () {
    $this->delete(route('users.destroy', 123123))
        ->assertRedirectToRoute('login');
});

test('super admins can delete users', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $this->actingAs($admin);

    $user = User::factory()->create();

    $this->assertDatabaseCount('users', 2);

    $this->delete(route('users.destroy', $user))
        ->assertRedirectBack()
        ->assertSessionHas('success', 'The user #'.$user->id.' has been deleted.');

    $this->assertDatabaseCount('users', 1);
});

test('non super admins cannot delete users', function () {
    $user1 = User::factory()->create();
    $this->actingAs($user1);

    $user2 = User::factory()->create();

    $this->assertDatabaseCount('users', 2);

    $this->delete(route('users.destroy', $user2))
        ->assertForbidden();

    $this->assertDatabaseCount('users', 2);
});

test('super admins cannot delete other super admins', function () {
    $admin1 = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $this->actingAs($admin1);

    $admin2 = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);

    $this->delete(route('users.destroy', $admin2))
        ->assertForbidden();

    $this->assertDatabaseCount('users', 2);
});

test('last super admin cannot delete themselves', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);
    $this->actingAs($admin);

    $this->delete(route('users.destroy', $admin))
        ->assertForbidden();

    $this->assertDatabaseCount('users', 1);
});

test('delete users is rate limited', function () {
    $admin = User::factory()->create()
        ->syncRoles([Role::SuperAdmin->value]);

    exhaustRateLimit('users-manage', (string) $admin->id, maxAttempts: 10);

    $this->actingAs($admin)
        ->delete(route('users.destroy', 123))
        ->assertTooManyRequests();
});

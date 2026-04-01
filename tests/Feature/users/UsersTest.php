<?php

use App\Enums\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('super admins can visit the users page', function () {
    $user = User::factory()->create()
        ->removeRole(Role::User->value)
        ->assignRole(Role::SuperAdmin->value);
    $this->actingAs($user);

    $this->get(route('users.index'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $component) {
            $component->component('users/index');
        });
});

test('non super admins cannot visit the users page', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('users.index'))
        ->assertForbidden();
});

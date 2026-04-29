<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Contact;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

// TODO: delete contacts is rate limited

it('redirects to login page when not authenticated', function () {
    $this->delete(route('contacts.destroy', 123))
        ->assertRedirectToRoute('login');
});

it('redirects to email verification page when email is not verified', function () {
    $user = User::factory()->unverified()->create();

    $contact = Contact::factory()->create([
        'user_id' => $user->id
    ]);

    $this->actingAs($user)
        ->delete(route('contacts.destroy', $contact))
        ->assertRedirectToRoute('verification.notice');
});

test('admins and managers can delete any contact', function () {
    $admin = User::factory()
        ->create()
        ->syncRoles([UserRole::SuperAdmin->value]);

    $manager = User::factory()
        ->create()
        ->syncRoles([UserRole::Manager->value]);

    $contacts = Contact::factory(2)->create();

    $this->actingAs($admin)
        ->delete(route('contacts.destroy', $contacts[0]))
        ->assertRedirectToRoute('contacts.index')
        ->assertSessionHas('success', 'The contact #'.$contacts[0]->id.' has been deleted.');

    $this->actingAs($manager)
        ->delete(route('contacts.destroy', $contacts[1]))
        ->assertRedirectToRoute('contacts.index')
        ->assertSessionHas('success', 'The contact #'.$contacts[1]->id.' has been deleted.');
});

test('non admins and managers cannot delete contacts', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    $user = User::factory()
        ->create();

    $contact = Contact::factory()->create();

    foreach ([$salesAgent, $user] as $member) {
        $this->actingAs($member)
            ->delete(route('contacts.destroy', $contact))
            ->assertForbidden();
    }
});

test('delete contacts is rate limited', function () {
    $salesAgent = User::factory()
        ->create()
        ->syncRoles([UserRole::SalesAgent->value]);

    exhaustRateLimit('contacts-manage', (string) $salesAgent->id, 10);

    $contact = Contact::factory()->create();

    $this->actingAs($salesAgent)
        ->delete(route('contacts.destroy', $contact))
        ->assertTooManyRequests();
});


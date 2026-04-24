<?php

declare(strict_types=1);

use App\Models\Account;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia;

test('guests are redirected to the login page', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('dashboard')
                ->has('metrics')
                ->has('metrics.stats')
                ->has('metrics.recent_accounts')
                ->has('metrics.stats.my_accounts')
                ->has('metrics.stats.accounts_this_month');
        });
});

test('dashboard shows user their own accounts count', function () {
    $agent = User::factory()->create()->syncRoles(['sales_agent']);
    Account::factory()->count(3)->create(['user_id' => $agent->id]);
    Account::factory()->count(5)->create();

    $this->actingAs($agent);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('dashboard')
                ->where('metrics.stats.my_accounts', 3)
                ->where('metrics.stats.accounts_this_month', 3)
                ->missing('metrics.stats.total_accounts')
                ->missing('metrics.stats.total_users')
                ->missing('metrics.role_distribution');
        });
});

test('manager sees total accounts but not total users', function () {
    $manager = User::factory()->create()->syncRoles(['manager']);
    Account::factory()->count(7)->create();

    $this->actingAs($manager);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('dashboard')
                ->where('metrics.stats.total_accounts', 7)
                ->missing('metrics.stats.total_users')
                ->missing('metrics.role_distribution');
        });
});

test('super admin sees total users and role distribution', function () {
    Cache::flush();

    $admin = User::factory()->create()->syncRoles(['super_admin']);
    $agent1 = User::factory()->create()->syncRoles(['sales_agent']);
    $agent2 = User::factory()->create()->syncRoles(['sales_agent']);
    $manager = User::factory()->create()->syncRoles(['manager']);
    Account::factory()->count(4)->create(['user_id' => $admin->id]);

    $this->actingAs($admin);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('dashboard')
                ->where('metrics.stats.total_accounts', 4)
                ->where('metrics.stats.total_users', 4)
                ->has('metrics.role_distribution');
        });
});

test('dashboard shows recent accounts scoped to user role', function () {
    $agent = User::factory()->create()->syncRoles(['sales_agent']);
    $otherAgent = User::factory()->create()->syncRoles(['sales_agent']);
    Account::factory()->count(3)->create(['user_id' => $agent->id]);
    Account::factory()->count(2)->create(['user_id' => $otherAgent->id]);

    $this->actingAs($agent);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('dashboard')
                ->has('metrics.recent_accounts.data', 3);
        });
});

test('manager sees all recent accounts across team', function () {
    $manager = User::factory()->create()->syncRoles(['manager']);
    Account::factory()->count(5)->create();

    $this->actingAs($manager);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(static function (AssertableInertia $page) {
            $page->component('dashboard')
                ->has('metrics.recent_accounts.data', 5);
        });
});

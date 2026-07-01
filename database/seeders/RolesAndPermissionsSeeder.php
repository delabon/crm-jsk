<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRole as RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

        // --- Dashboard ---
        Permission::create(['name' => 'dashboard.view']);
        Permission::create(['name' => 'profile.manage']);

        // --- Accounts ---
        Permission::create(['name' => 'accounts.view-any']);
        Permission::create(['name' => 'accounts.view-own']);
        Permission::create(['name' => 'accounts.create']);
        Permission::create(['name' => 'accounts.update']);
        Permission::create(['name' => 'accounts.delete']);

        // --- Contacts ---
        Permission::create(['name' => 'contacts.view-any']);
        Permission::create(['name' => 'contacts.view-own']);
        Permission::create(['name' => 'contacts.create']);
        Permission::create(['name' => 'contacts.update']);
        Permission::create(['name' => 'contacts.delete']);

        // --- Deals ---
        Permission::create(['name' => 'deals.view-any']);
        Permission::create(['name' => 'deals.view-own']);
        Permission::create(['name' => 'deals.create']);
        Permission::create(['name' => 'deals.update']);
        Permission::create(['name' => 'deals.delete']);

        // --- Campaigns ---
        Permission::create(['name' => 'campaigns.view']);
        Permission::create(['name' => 'campaigns.manage']);

        // --- Tasks ---
        Permission::create(['name' => 'tasks.manage']);

        // --- Users ---
        Permission::create(['name' => 'users.manage']);

        // --- Reports ---
        Permission::create(['name' => 'reports.own']);
        Permission::create(['name' => 'reports.team']);
        Permission::create(['name' => 'reports.global']);

        // --- Roles ---
        $admin = Role::create(['name' => RoleEnum::SuperAdmin->value]);
        $admin->givePermissionTo(Permission::all());

        $manager = Role::create(['name' => RoleEnum::Manager->value]);
        $manager->givePermissionTo([
            'dashboard.view',
            'profile.manage',
            // Accounts
            'accounts.view-any',
            'accounts.view-own',
            'accounts.create',
            'accounts.update',
            'accounts.delete',
            // Contacts
            'contacts.view-any',
            'contacts.view-own',
            'contacts.create',
            'contacts.update',
            'contacts.delete',
            // Deals
            'deals.view-any',
            'deals.create',
            'deals.update',
            'deals.delete',
            // Campaigns
            'campaigns.view',
            'campaigns.manage',
            // Tasks
            'tasks.manage',
            // Reports
            'reports.own',
            'reports.team',
        ]);

        $salesAgent = Role::create(['name' => RoleEnum::SalesAgent->value]);
        $salesAgent->givePermissionTo([
            'dashboard.view',
            'profile.manage',
            'accounts.view-own',
            'accounts.create',
            'accounts.update',
            'contacts.view-own',
            'contacts.create',
            'contacts.update',
            'deals.view-own',
            'deals.create',
            'deals.update',
            'tasks.manage',
            'reports.own',
        ]);

        $userRole = Role::create(['name' => RoleEnum::User->value]);
        $userRole->givePermissionTo([
            'dashboard.view',
            'profile.manage',
        ]);
    }
}

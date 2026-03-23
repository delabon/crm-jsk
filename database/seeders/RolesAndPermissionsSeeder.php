<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use App\Enums\Role as RoleEnum;

final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();

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
            'contacts.view-any',
            'contacts.create',
            'contacts.update',
            'contacts.delete',
            'deals.view-any',
            'deals.create',
            'deals.update',
            'deals.delete',
            'campaigns.view',
            'campaigns.manage',
            'tasks.manage',
            'reports.own',
            'reports.team',
        ]);

        $salesAgent = Role::create(['name' => RoleEnum::SalesAgent->value]);
        $salesAgent->givePermissionTo([
            'contacts.view-own',
            'contacts.create',
            'contacts.update',
            'deals.view-own',
            'deals.create',
            'deals.update',
            'tasks.manage',
            'reports.own',
        ]);
    }
}

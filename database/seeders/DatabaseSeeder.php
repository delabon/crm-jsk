<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super.admin@example.com',
        ])->assignRole(Role::SuperAdmin);

        User::factory()->create([
            'first_name' => 'Manager',
            'last_name' => 'User',
            'email' => 'manager@example.com',
        ])->assignRole(Role::Manager);

        User::factory()->create([
            'first_name' => 'Sales',
            'last_name' => 'Agent',
            'email' => 'sales.agent@example.com',
        ])->assignRole(Role::SalesAgent);

        // User::factory()->create([
        //     'first_name' => 'Sales',
        //     'last_name' => 'Agent',
        //     'email' => 'sales.agent@example.com',
        // ])->assignRole(Role::SalesAgent);
    }
}

<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
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
        ])->syncRoles(UserRole::SuperAdmin->value);

        User::factory()->create([
            'first_name' => 'Manager',
            'last_name' => 'Account',
            'email' => 'manager@example.com',
        ])->syncRoles(UserRole::Manager->value);

        User::factory()->create([
            'first_name' => 'Sales',
            'last_name' => 'Agent',
            'email' => 'sales.agent@example.com',
        ])->syncRoles(UserRole::SalesAgent->value);
    }
}

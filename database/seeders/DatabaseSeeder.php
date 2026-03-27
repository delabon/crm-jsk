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
        /** @var User $superAdmin */
        $superAdmin = User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'super.admin@example.com',
        ]);

        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        $superAdmin->assignRole(Role::SuperAdmin);

        // User::factory()->create([
        //     'first_name' => 'Sales',
        //     'last_name' => 'Agent',
        //     'email' => 'sales.agent@example.com',
        // ])->assignRole(Role::SalesAgent);
    }
}

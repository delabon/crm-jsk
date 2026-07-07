<?php

declare(strict_types=1);

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Enums\UserRole as RoleEnum;
use App\Models\Account;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            RoleEnum::SuperAdmin,
            RoleEnum::Manager,
            RoleEnum::SalesAgent,
        ];

        foreach ($roles as $role) {
            // Contacts
            User::role($role->value)
                ->get()
                ->each(static function (User $user) {
                    Contact::factory(15)
                        ->create([
                            'user_id' => $user->id,
                        ])
                        ->each(static function (Contact $contact) {
                            Address::factory()
                                ->forContact($contact)
                                ->create();
                        });

                    Account::factory(15)
                        ->create([
                            'user_id' => $user->id,
                        ])
                        ->each(static function (Account $account) {
                            Address::factory()
                                ->forAccount($account)
                                ->create();
                        });
                });
        }

    }
}

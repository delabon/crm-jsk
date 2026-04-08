<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Account>
 */
final class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->company();
        $website = mb_strtolower(preg_replace('/[^a-z]/i', '-', $name).'.cc');

        return [
            'user_id' => User::factory(),
            'name' => $name,
            'industry' => fake()->randomElement([
                'Technology',
                'Healthcare',
                'Finance',
                'Manufacturing',
                'Energy',
            ]),
            'website' => $website,
            'phone' => fake()->phoneNumber(),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Account;
use App\Models\Address;
use App\Models\Contact;
use App\Models\Region;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Address>
 */
final class AddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /** @var Region $region */
        $region = Region::query()->inRandomOrder()->first();

        return [
            'name' => fake()->randomElement(['HQ', 'Billing', 'Warehouse', 'Office', 'Home']),
            'line1' => fake()->streetAddress(),
            'line2' => fake()->streetAddress(),
            'city' => fake()->city(),
            'region_id' => $region->id,
            'country_id' => $region->country_id,
            'postal_code' => fake()->postcode(),
        ];
    }

    /**
     * Attach the address to an account (defaults to a new account).
     */
    public function forAccount(?Account $account = null): static
    {
        return $this->for($account ?? Account::factory(), 'addressable');
    }

    /**
     * Attach the address to a contact (defaults to a new contact).
     */
    public function forContact(?Contact $contact = null): static
    {
        return $this->for($contact ?? Contact::factory(), 'addressable');
    }
}

<?php

namespace Database\Factories;

use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProfile>
 */
class ClientProfileFactory extends Factory
{
    protected $model = ClientProfile::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_name' => fake()->company(),
            'contact_person' => fake()->name(),
            'industry' => fake()->word(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'gst_vat_number' => 'GST' . fake()->numerify('##########'),
            'tax_id' => fake()->numerify('TAX-#####'),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'country' => fake()->country(),
        ];
    }
}

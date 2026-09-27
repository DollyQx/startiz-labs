<?php

namespace Database\Factories;

use App\Enums\QuotationStatus;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Quotation>
 */
class QuotationFactory extends Factory
{
    protected $model = Quotation::class;

    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'reference_number' => 'QUO-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'issue_date' => now(),
            'valid_until' => now()->addDays(30),
            'subtotal' => 1000.00,
            'discount' => 0.00,
            'tax' => 180.00,
            'total' => 1180.00,
            'status' => QuotationStatus::DRAFT,
            'notes' => $this->faker->sentence(),
            'terms' => $this->faker->sentence(),
        ];
    }
}

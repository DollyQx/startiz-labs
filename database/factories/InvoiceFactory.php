<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'reference_number' => 'STZ-INV-' . strtoupper(Str::random(8)),
            'issue_date' => now(),
            'due_date' => now()->addDays(14),
            'subtotal' => 1000.00,
            'discount' => 0.00,
            'tax' => 180.00,
            'total' => 1180.00,
            'amount_paid' => 0.00,
            'amount_due' => 1180.00,
            'status' => InvoiceStatus::ISSUED,
            'notes' => $this->faker->sentence(),
        ];
    }
}

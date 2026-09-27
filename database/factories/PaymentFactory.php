<?php

namespace Database\Factories;

use App\Enums\PaymentStatus;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'client_id' => User::factory(),
            'invoice_id' => Invoice::factory(),
            'reference_number' => 'STZ-PAY-' . strtoupper(Str::random(8)),
            'amount' => 500.00,
            'currency' => 'INR',
            'payment_method' => 'bank_transfer',
            'provider' => 'manual',
            'status' => PaymentStatus::PAID,
            'paid_at' => now(),
            'notes' => $this->faker->sentence(),
        ];
    }
}

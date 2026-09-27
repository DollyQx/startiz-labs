<?php

namespace Database\Factories;

use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\QuotationItem>
 */
class QuotationItemFactory extends Factory
{
    protected $model = QuotationItem::class;

    public function definition(): array
    {
        $qty = 1;
        $unitPrice = 1000.00;
        $discount = 0.00;
        $tax = 180.00;

        return [
            'quotation_id' => Quotation::factory(),
            'service_id' => null,
            'description' => $this->faker->sentence(),
            'quantity' => $qty,
            'unit_price' => $unitPrice,
            'discount' => $discount,
            'tax' => $tax,
            'line_total' => ($qty * $unitPrice) - $discount + $tax,
            'sequence_order' => 1,
        ];
    }
}

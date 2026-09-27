<?php

namespace App\Http\Requests\Admin;

use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'gt:0'],
            'payment_method' => ['required', 'string', 'max:50'],
            'paid_at' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $invoice = $this->route('invoice');
            if ($invoice) {
                if ($invoice->status->value === InvoiceStatus::PAID->value) {
                    $validator->errors()->add('amount', 'This invoice has already been fully paid.');
                }
                if ($invoice->status->value === InvoiceStatus::CANCELLED->value) {
                    $validator->errors()->add('amount', 'Cannot record payment for a cancelled invoice.');
                }
                $amount = (float) $this->input('amount');
                $amountDue = (float) $invoice->amount_due;
                if ($amount > $amountDue + 0.01) { // 1 cent buffer for float rounding
                    $validator->errors()->add('amount', 'Payment amount (₹' . number_format($amount, 2) . ') cannot exceed invoice amount due (₹' . number_format($amountDue, 2) . ').');
                }
            }
        });
    }
}

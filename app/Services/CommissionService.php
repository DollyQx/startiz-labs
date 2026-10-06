<?php

namespace App\Services;

use App\Enums\CommissionStatus;
use App\Enums\PayoutStatus;
use App\Models\Commission;
use App\Models\Invoice;
use App\Models\PartnerProfile;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Project;
use App\Models\Referral;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class CommissionService
{
    /**
     * Calculate commission details for a partner and given base amount.
     */
    public function calculateCommission(PartnerProfile $partner, float $baseAmount): array
    {
        $rate = (float) ($partner->commission_rate ?? config('partner.default_commission_rate', 20.00));
        $commissionAmount = round($baseAmount * ($rate / 100), 2);

        return [
            'base_amount' => round($baseAmount, 2),
            'rate' => $rate,
            'commission_amount' => $commissionAmount,
            'currency' => config('partner.currency', 'INR'),
        ];
    }

    /**
     * Create a commission record for a referral and base amount.
     * Starts in PENDING state. Crucial: NEVER automatically marks as PAID.
     */
    public function createCommission(
        Referral $referral,
        float $baseAmount,
        ?Project $project = null,
        ?Invoice $invoice = null,
        ?Payment $payment = null,
        ?string $notes = null
    ): Commission {
        $partner = $referral->partner;
        $calc = $this->calculateCommission($partner, $baseAmount);

        return Commission::create([
            'partner_id' => $partner->id,
            'referral_id' => $referral->id,
            'project_id' => $project?->id,
            'invoice_id' => $invoice?->id,
            'payment_id' => $payment?->id,
            'base_amount' => $calc['base_amount'],
            'commission_rate' => $calc['rate'],
            'commission_amount' => $calc['commission_amount'],
            'currency' => $calc['currency'],
            'status' => CommissionStatus::PENDING,
            'notes' => $notes,
        ]);
    }

    /**
     * Approve a pending commission.
     */
    public function approveCommission(Commission $commission, User $admin, ?string $notes = null): Commission
    {
        if ($commission->status !== CommissionStatus::PENDING) {
            throw new InvalidArgumentException("Only pending commissions can be approved. Current status: {$commission->status->value}");
        }

        $commission->update([
            'status' => CommissionStatus::APPROVED,
            'approved_at' => now(),
            'approved_by_id' => $admin->id,
            'notes' => $notes ? trim($commission->notes . "\n" . $notes) : $commission->notes,
        ]);

        return $commission->fresh();
    }

    /**
     * Move an approved commission to payable status.
     */
    public function markCommissionPayable(Commission $commission, User $admin): Commission
    {
        if (! in_array($commission->status, [CommissionStatus::PENDING, CommissionStatus::APPROVED], true)) {
            throw new InvalidArgumentException("Commission cannot be marked payable from status: {$commission->status->value}");
        }

        $commission->update([
            'status' => CommissionStatus::PAYABLE,
            'payable_at' => now(),
            'approved_at' => $commission->approved_at ?? now(),
            'approved_by_id' => $commission->approved_by_id ?? $admin->id,
        ]);

        return $commission->fresh();
    }

    /**
     * Reject a commission with a reason.
     */
    public function rejectCommission(Commission $commission, User $admin, string $reason): Commission
    {
        if ($commission->status === CommissionStatus::PAID) {
            throw new InvalidArgumentException("Paid commissions cannot be rejected.");
        }

        $commission->update([
            'status' => CommissionStatus::REJECTED,
            'rejection_reason' => $reason,
        ]);

        return $commission->fresh();
    }

    /**
     * Process a payout for approved/payable commissions.
     * Transitions associated commissions to PAID.
     */
    public function processPayout(
        PartnerProfile $partner,
        array $commissionIds,
        array $payoutData,
        User $admin
    ): Payout {
        return DB::transaction(function () use ($partner, $commissionIds, $payoutData, $admin) {
            $commissions = Commission::where('partner_id', $partner->id)
                ->whereIn('id', $commissionIds)
                ->whereIn('status', [CommissionStatus::APPROVED->value, CommissionStatus::PAYABLE->value])
                ->lockForUpdate()
                ->get();

            if ($commissions->isEmpty()) {
                throw new InvalidArgumentException("No eligible approved or payable commissions selected for payout.");
            }

            $totalAmount = $commissions->sum('commission_amount');

            $payout = Payout::create([
                'partner_id' => $partner->id,
                'amount' => $totalAmount,
                'currency' => config('partner.currency', 'INR'),
                'payment_method' => $payoutData['payment_method'] ?? 'bank_transfer',
                'transaction_reference' => $payoutData['transaction_reference'] ?? null,
                'status' => PayoutStatus::COMPLETED,
                'notes' => $payoutData['notes'] ?? null,
                'processed_by_id' => $admin->id,
                'processed_at' => now(),
            ]);

            // Mark each commission as PAID linked to this payout
            foreach ($commissions as $comm) {
                $comm->update([
                    'status' => CommissionStatus::PAID,
                    'payout_id' => $payout->id,
                    'paid_at' => now(),
                ]);
            }

            return $payout;
        });
    }
}

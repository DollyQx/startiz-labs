<?php

namespace App\Services;

use App\Enums\CommissionStatus;
use App\Enums\PartnerStatus;
use App\Enums\ReferralStatus;
use App\Models\PartnerProfile;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeaderboardService
{
    /**
     * Compute the real leaderboard based purely on database records.
     * Periods: 'all', 'month', 'year'.
     */
    public function getLeaderboard(string $period = 'all'): Collection
    {
        $startDate = match ($period) {
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => null,
        };

        $partners = PartnerProfile::with('user')
            ->where('status', PartnerStatus::APPROVED->value)
            ->get();

        $rows = $partners->map(function (PartnerProfile $partner) use ($startDate) {
            // Count converted referrals
            $conversionsQuery = $partner->referrals()
                ->where('status', ReferralStatus::CONVERTED->value);

            if ($startDate) {
                $conversionsQuery->where('converted_at', '>=', $startDate);
            }

            $conversionsCount = $conversionsQuery->count();

            // Sum qualifying earnings (approved, payable, paid)
            $commissionsQuery = $partner->commissions()
                ->whereIn('status', [
                    CommissionStatus::APPROVED->value,
                    CommissionStatus::PAYABLE->value,
                    CommissionStatus::PAID->value,
                ]);

            if ($startDate) {
                $commissionsQuery->where('created_at', '>=', $startDate);
            }

            $earnings = (float) $commissionsQuery->sum('commission_amount');

            return [
                'rank' => 0,
                'partner_id' => $partner->id,
                'user_id' => $partner->user_id,
                'name' => $partner->user->name ?? 'Partner #' . $partner->id,
                'partner_name' => $partner->user->name ?? 'Partner #' . $partner->id,
                'company_name' => $partner->company_name,
                'referral_code' => $partner->referral_code,
                'conversions' => $conversionsCount,
                'conversions_count' => $conversionsCount,
                'earnings' => $earnings,
                'total_earnings' => $earnings,
                'formatted_earnings' => number_format($earnings, 2),
            ];
        });

        // Sort by earnings descending, then conversions count descending, then partner_id ascending
        $sorted = $rows->sort(function ($a, $b) {
            if ($b['total_earnings'] <=> $a['total_earnings']) {
                return $b['total_earnings'] <=> $a['total_earnings'];
            }
            if ($b['conversions_count'] <=> $a['conversions_count']) {
                return $b['conversions_count'] <=> $a['conversions_count'];
            }
            return $a['partner_id'] <=> $b['partner_id'];
        })->values();

        // Assign real rank
        return $sorted->map(function ($row, $index) {
            $row['rank'] = $index + 1;
            return $row;
        });
    }

    /**
     * Get the leaderboard rank of a specific partner.
     */
    public function getPartnerRank(PartnerProfile $partner, string $period = 'all'): int
    {
        $board = $this->getLeaderboard($period);
        $entry = $board->firstWhere('partner_id', $partner->id);

        return $entry ? (int) $entry['rank'] : 1;
    }
}

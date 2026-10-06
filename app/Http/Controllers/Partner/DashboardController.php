<?php

namespace App\Http\Controllers\Partner;

use App\Enums\CommissionStatus;
use App\Enums\ReferralStatus;
use App\Http\Controllers\Controller;
use App\Services\LeaderboardService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected LeaderboardService $leaderboardService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $partner = $user->partnerProfile;

        // Metrics required by prompt
        $totalReferrals = $partner->referrals()->count();
        $activeLeads = $partner->referrals()
            ->whereIn('status', [ReferralStatus::NEW->value, ReferralStatus::CONTACTED->value])
            ->count();
        $convertedClients = $partner->referrals()
            ->where('status', ReferralStatus::CONVERTED->value)
            ->count();

        $totalBusinessGenerated = (float) $partner->commissions()
            ->whereIn('status', [
                CommissionStatus::APPROVED->value,
                CommissionStatus::PAYABLE->value,
                CommissionStatus::PAID->value,
            ])
            ->sum('base_amount');

        $pendingCommission = (float) $partner->commissions()
            ->where('status', CommissionStatus::PENDING->value)
            ->sum('commission_amount');

        $approvedCommission = (float) $partner->commissions()
            ->whereIn('status', [
                CommissionStatus::APPROVED->value,
                CommissionStatus::PAYABLE->value,
            ])
            ->sum('commission_amount');

        $paidCommission = (float) $partner->commissions()
            ->where('status', CommissionStatus::PAID->value)
            ->sum('commission_amount');

        $totalEarnings = (float) $partner->commissions()
            ->whereIn('status', [
                CommissionStatus::APPROVED->value,
                CommissionStatus::PAYABLE->value,
                CommissionStatus::PAID->value,
            ])
            ->sum('commission_amount');

        $currentRank = $this->leaderboardService->getPartnerRank($partner, 'all');

        $recentReferrals = $partner->referrals()
            ->latest()
            ->take(6)
            ->get();

        $recentCommissions = $partner->commissions()
            ->with('referral')
            ->latest()
            ->take(6)
            ->get();

        return view('partner.dashboard', compact(
            'partner',
            'totalReferrals',
            'activeLeads',
            'convertedClients',
            'totalBusinessGenerated',
            'pendingCommission',
            'approvedCommission',
            'paidCommission',
            'totalEarnings',
            'currentRank',
            'recentReferrals',
            'recentCommissions'
        ));
    }
}

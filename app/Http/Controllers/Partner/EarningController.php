<?php

namespace App\Http\Controllers\Partner;

use App\Enums\CommissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EarningController extends Controller
{
    public function index(Request $request): View
    {
        $partner = $request->user()->partnerProfile;

        $query = $partner->commissions()->with('referral', 'payout')->latest();

        $statusFilter = $request->query('status');
        if ($statusFilter && CommissionStatus::tryFrom($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $commissions = $query->paginate(15, ['*'], 'commissions_page')->withQueryString();

        $payouts = $partner->payouts()->latest()->paginate(10, ['*'], 'payouts_page')->withQueryString();

        $pendingAmount = (float) $partner->commissions()
            ->where('status', CommissionStatus::PENDING->value)
            ->sum('commission_amount');

        $approvedAmount = (float) $partner->commissions()
            ->whereIn('status', [CommissionStatus::APPROVED->value, CommissionStatus::PAYABLE->value])
            ->sum('commission_amount');

        $paidAmount = (float) $partner->commissions()
            ->where('status', CommissionStatus::PAID->value)
            ->sum('commission_amount');

        $totalEarnings = (float) $partner->commissions()
            ->whereIn('status', [CommissionStatus::APPROVED->value, CommissionStatus::PAYABLE->value, CommissionStatus::PAID->value])
            ->sum('commission_amount');

        return view('partner.earnings.index', compact(
            'partner',
            'commissions',
            'payouts',
            'pendingAmount',
            'approvedAmount',
            'paidAmount',
            'totalEarnings',
            'statusFilter'
        ));
    }

    public function show(Request $request, Commission $commission): View
    {
        $partner = $request->user()->partnerProfile;

        // Security: Partner data isolation
        if ($commission->partner_id !== $partner->id) {
            abort(403, 'Unauthorized access to this commission.');
        }

        return view('partner.earnings.show', compact('partner', 'commission'));
    }
}

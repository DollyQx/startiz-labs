<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\PartnerProfile;
use App\Models\Payout;
use App\Services\CommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPartnerPayoutController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService
    ) {}

    public function index(Request $request): View
    {
        $payouts = Payout::with('partner.user', 'processedBy', 'commissions')
            ->latest()
            ->paginate(20);

        $payablePartners = PartnerProfile::with(['user', 'commissions' => function ($q) {
            $q->whereIn('status', ['approved', 'payable']);
        }])->get()->filter(function ($partner) {
            return $partner->commissions->isNotEmpty();
        });

        return view('admin.partners.payouts', compact('payouts', 'payablePartners'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'partner_id' => ['required', 'exists:partner_profiles,id'],
            'commission_ids' => ['required', 'array', 'min:1'],
            'commission_ids.*' => ['exists:commissions,id'],
            'payment_method' => ['required', 'string', 'in:bank_transfer,upi,manual'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $partner = PartnerProfile::findOrFail($request->partner_id);

        $payout = $this->commissionService->processPayout(
            $partner,
            $request->commission_ids,
            [
                'payment_method' => $request->payment_method,
                'transaction_reference' => $request->transaction_reference,
                'notes' => $request->notes,
            ],
            $request->user()
        );

        return back()->with('status', "Payout {$payout->reference_number} of " . number_format($payout->amount, 2) . " processed successfully.");
    }
}

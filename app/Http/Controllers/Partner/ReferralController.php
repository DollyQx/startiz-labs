<?php

namespace App\Http\Controllers\Partner;

use App\Enums\ReferralStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Partner\StoreManualReferralRequest;
use App\Models\Referral;
use App\Services\ReferralService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferralController extends Controller
{
    public function create(Request $request): View
    {
        $partner = $request->user()->partnerProfile;

        $services = [
            'Website Development',
            'Web Applications',
            'Mobile Applications',
            'E-commerce',
            'Custom Software',
            'Cybersecurity',
            'Other Digital Solutions',
        ];

        return view('partner.referrals.create', compact('partner', 'services'));
    }

    public function store(StoreManualReferralRequest $request, ReferralService $referralService): RedirectResponse
    {
        $partner = $request->user()->partnerProfile;

        try {
            $referralService->submitManualReferral($partner, $request->validated());

            return redirect()->route('partner.referrals.index')
                ->with('status', 'Referral submitted successfully.');
        } catch (\DomainException $e) {
            return back()->withInput()->withErrors(['email' => $e->getMessage()]);
        }
    }
    public function index(Request $request): View
    {
        $partner = $request->user()->partnerProfile;

        $query = $partner->referrals()->latest();

        $statusFilter = $request->query('status');
        if ($statusFilter && ReferralStatus::tryFrom($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $referrals = $query->paginate(15)->withQueryString();

        $counts = [
            'all' => $partner->referrals()->count(),
            'new' => $partner->referrals()->where('status', ReferralStatus::NEW->value)->count(),
            'contacted' => $partner->referrals()->where('status', ReferralStatus::CONTACTED->value)->count(),
            'converted' => $partner->referrals()->where('status', ReferralStatus::CONVERTED->value)->count(),
            'lost' => $partner->referrals()->where('status', ReferralStatus::LOST->value)->count(),
        ];

        return view('partner.referrals.index', compact('partner', 'referrals', 'counts', 'statusFilter'));
    }

    public function show(Request $request, Referral $referral): View
    {
        $partner = $request->user()->partnerProfile;

        // Security: Partner data isolation - strictly own referrals only
        if ($referral->partner_id !== $partner->id) {
            abort(403, 'Unauthorized access to this referral.');
        }

        $commissions = $referral->commissions()->latest()->get();

        return view('partner.referrals.show', compact('partner', 'referral', 'commissions'));
    }
}

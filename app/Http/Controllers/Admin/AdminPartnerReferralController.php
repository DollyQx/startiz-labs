<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ReferralStatus;
use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPartnerReferralController extends Controller
{
    public function index(Request $request): View
    {
        $query = Referral::with('partner.user', 'client', 'lead')->latest();

        $statusFilter = $request->query('status');
        if ($statusFilter && ReferralStatus::tryFrom($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%")
                    ->orWhere('client_name', 'like', "%{$search}%")
                    ->orWhere('client_email', 'like', "%{$search}%");
            });
        }

        $referrals = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => Referral::count(),
            'new' => Referral::where('status', ReferralStatus::NEW->value)->count(),
            'contacted' => Referral::where('status', ReferralStatus::CONTACTED->value)->count(),
            'converted' => Referral::where('status', ReferralStatus::CONVERTED->value)->count(),
            'lost' => Referral::where('status', ReferralStatus::LOST->value)->count(),
        ];

        return view('admin.partners.referrals', compact('referrals', 'stats', 'statusFilter', 'search'));
    }

    public function updateStatus(Request $request, Referral $referral): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:new,contacted,converted,lost'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = ReferralStatus::from($request->status);

        $referral->update([
            'status' => $status,
            'converted_at' => ($status === ReferralStatus::CONVERTED && ! $referral->converted_at) ? now() : $referral->converted_at,
            'notes' => $request->notes ?? $referral->notes,
        ]);

        return back()->with('status', "Referral status updated to {$status->label()}.");
    }
}

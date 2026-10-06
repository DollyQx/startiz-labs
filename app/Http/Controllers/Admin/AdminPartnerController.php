<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PartnerStatus;
use App\Http\Controllers\Controller;
use App\Models\PartnerProfile;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPartnerController extends Controller
{
    public function index(Request $request): View
    {
        $query = PartnerProfile::with('user', 'approvedBy')->latest();

        $statusFilter = $request->query('status');
        if ($statusFilter && PartnerStatus::tryFrom($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('referral_code', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        $partners = $query->paginate(15)->withQueryString();

        $stats = [
            'total' => PartnerProfile::count(),
            'pending' => PartnerProfile::where('status', PartnerStatus::PENDING->value)->count(),
            'approved' => PartnerProfile::where('status', PartnerStatus::APPROVED->value)->count(),
            'suspended' => PartnerProfile::where('status', PartnerStatus::SUSPENDED->value)->count(),
        ];

        return view('admin.partners.index', compact('partners', 'stats', 'statusFilter', 'search'));
    }

    public function show(PartnerProfile $partner): View
    {
        $partner->load('user', 'approvedBy', 'referrals', 'commissions.referral', 'payouts');

        $referrals = $partner->referrals()->latest()->paginate(10, ['*'], 'ref_page');
        $commissions = $partner->commissions()->with('referral', 'payout')->latest()->paginate(10, ['*'], 'comm_page');
        $payouts = $partner->payouts()->latest()->paginate(5, ['*'], 'payout_page');

        return view('admin.partners.show', compact('partner', 'referrals', 'commissions', 'payouts'));
    }

    public function updateStatus(Request $request, PartnerProfile $partner): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:pending,approved,suspended,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $newStatus = PartnerStatus::from($request->status);

        $updateData = [
            'status' => $newStatus,
        ];

        if ($newStatus === PartnerStatus::APPROVED) {
            $updateData['approved_at'] = now();
            $updateData['approved_by_id'] = $request->user()->id;
            $updateData['rejection_reason'] = null;

            if ($partner->user) {
                NotificationService::notifyUser(
                    $partner->user,
                    'system',
                    '🎉 Partner Account Approved',
                    "Congratulations! Your Startiz Labs partner account has been approved at a {$partner->commission_rate}% commission rate. Share your link to start earning.",
                    route('partner.dashboard'),
                    $partner
                );
            }
        } elseif ($newStatus === PartnerStatus::REJECTED) {
            $updateData['rejection_reason'] = $request->rejection_reason;
        }

        $partner->update($updateData);

        return back()->with('status', "Partner account marked as {$newStatus->label()}.");
    }

    public function updateCommissionRate(Request $request, PartnerProfile $partner): RedirectResponse
    {
        $request->validate([
            'commission_rate' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $partner->update([
            'commission_rate' => $request->commission_rate,
        ]);

        return back()->with('status', "Commission rate updated to {$partner->commission_rate}%.");
    }
}

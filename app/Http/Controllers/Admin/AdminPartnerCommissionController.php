<?php

namespace App\Http\Controllers\Admin;

use App\Enums\CommissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\PartnerProfile;
use App\Models\Project;
use App\Models\Referral;
use App\Services\CommissionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminPartnerCommissionController extends Controller
{
    public function __construct(
        protected CommissionService $commissionService
    ) {}

    public function index(Request $request): View
    {
        $query = Commission::with('partner.user', 'referral', 'project', 'payout')->latest();

        $statusFilter = $request->query('status');
        if ($statusFilter && CommissionStatus::tryFrom($statusFilter)) {
            $query->where('status', $statusFilter);
        }

        $commissions = $query->paginate(20)->withQueryString();

        $partners = PartnerProfile::with('user')->where('status', 'approved')->get();
        $referrals = Referral::where('status', 'converted')->get();

        $stats = [
            'total' => Commission::count(),
            'pending' => Commission::where('status', CommissionStatus::PENDING->value)->count(),
            'approved' => Commission::whereIn('status', [CommissionStatus::APPROVED->value, CommissionStatus::PAYABLE->value])->count(),
            'paid' => Commission::where('status', CommissionStatus::PAID->value)->count(),
            'total_paid_amount' => (float) Commission::where('status', CommissionStatus::PAID->value)->sum('commission_amount'),
        ];

        return view('admin.partners.commissions', compact('commissions', 'partners', 'referrals', 'stats', 'statusFilter'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'referral_id' => ['required', 'exists:referrals,id'],
            'base_amount' => ['required', 'numeric', 'min:1'],
            'project_id' => ['nullable', 'exists:projects,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $referral = Referral::findOrFail($request->referral_id);
        $project = $request->project_id ? Project::find($request->project_id) : null;

        $commission = $this->commissionService->createCommission(
            $referral,
            (float) $request->base_amount,
            $project,
            null,
            null,
            $request->notes
        );

        return back()->with('status', "Commission created in {$commission->status->label()} status.");
    }

    public function approve(Request $request, Commission $commission): RedirectResponse
    {
        $this->commissionService->approveCommission($commission, $request->user(), $request->input('notes'));

        return back()->with('status', 'Commission approved successfully.');
    }

    public function payable(Request $request, Commission $commission): RedirectResponse
    {
        $this->commissionService->markCommissionPayable($commission, $request->user());

        return back()->with('status', 'Commission marked as payable.');
    }

    public function reject(Request $request, Commission $commission): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'max:1000'],
        ]);

        $this->commissionService->rejectCommission($commission, $request->user(), $request->rejection_reason);

        return back()->with('status', 'Commission has been rejected.');
    }
}

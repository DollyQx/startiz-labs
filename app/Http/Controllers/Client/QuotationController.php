<?php

namespace App\Http\Controllers\Client;

use App\Enums\QuotationStatus;
use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class QuotationController extends Controller
{
    /**
     * Display a listing of client's quotations.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Quotation::class);

        $client = $request->user();

        $query = Quotation::where('client_id', $client->id)
            ->with(['project', 'items']);

        // Search by reference or project title
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhereHas('project', function ($pq) use ($search) {
                        $pq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $quotations = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $statuses = QuotationStatus::cases();

        return view('client.quotations.index', compact('quotations', 'statuses'));
    }

    /**
     * Display client quotation workspace.
     */
    public function show(Quotation $quotation): View
    {
        Gate::authorize('view', $quotation);

        // Auto transition status from SENT to VIEWED when client opens the quotation workspace
        if ($quotation->status === QuotationStatus::SENT) {
            $quotation->update(['status' => QuotationStatus::VIEWED]);
        }

        $quotation->load(['client', 'project', 'items']);

        return view('client.quotations.show', compact('quotation'));
    }

    /**
     * Accept a quotation (Server-side transition).
     */
    public function accept(Request $request, Quotation $quotation): RedirectResponse
    {
        Gate::authorize('accept', $quotation);

        if ($quotation->status === QuotationStatus::ACCEPTED) {
            return redirect()->back()->with('status', 'This quotation has already been accepted.');
        }

        // Only allow acceptance from SENT or VIEWED statuses
        if (!in_array($quotation->status, [QuotationStatus::SENT, QuotationStatus::VIEWED])) {
            return redirect()->back()->with('error', 'This quotation cannot be accepted in its current status (' . $quotation->status->label() . ').');
        }

        $quotation->update([
            'status' => QuotationStatus::ACCEPTED,
        ]);

        // Notifications
        \App\Services\NotificationService::notifyUser(
            auth()->user(),
            'quotation',
            "Quotation Accepted: {$quotation->reference_number}",
            "You accepted quotation #{$quotation->reference_number} for ₹" . number_format((float) $quotation->total, 2) . ".",
            route('client.quotations.show', $quotation->id),
            $quotation
        );

        \App\Services\NotificationService::notifyRoles(
            [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPER_ADMIN, \App\Enums\UserRole::PROJECT_MANAGER],
            'quotation',
            "Quotation Accepted by Client: {$quotation->reference_number}",
            "Client " . (auth()->user()->name ?? 'Client') . " accepted quotation #{$quotation->reference_number} for ₹" . number_format((float) $quotation->total, 2) . ".",
            route('admin.quotations.show', $quotation->id),
            $quotation
        );

        ActivityLogger::log(
            action: 'quotation.accepted',
            subject: $quotation,
            description: 'Client accepted quotation #' . $quotation->reference_number . ' for ₹' . number_format((float) $quotation->total, 2),
            metadata: [
                'total' => (float) $quotation->total,
                'quotation_reference' => $quotation->reference_number,
            ]
        );

        return redirect()->back()->with('status', 'Quotation accepted successfully! Our team has been notified.');
    }

    /**
     * Reject a quotation (Server-side transition).
     */
    public function reject(Request $request, Quotation $quotation): RedirectResponse
    {
        Gate::authorize('reject', $quotation);

        if ($quotation->status === QuotationStatus::REJECTED) {
            return redirect()->back()->with('status', 'This quotation has already been marked as rejected.');
        }

        // Only allow rejection from SENT or VIEWED statuses
        if (!in_array($quotation->status, [QuotationStatus::SENT, QuotationStatus::VIEWED])) {
            return redirect()->back()->with('error', 'This quotation cannot be rejected in its current status (' . $quotation->status->label() . ').');
        }

        $quotation->update([
            'status' => QuotationStatus::REJECTED,
        ]);

        // Notifications
        \App\Services\NotificationService::notifyUser(
            auth()->user(),
            'quotation',
            "Quotation Rejected: {$quotation->reference_number}",
            "You rejected quotation #{$quotation->reference_number}.",
            route('client.quotations.show', $quotation->id),
            $quotation
        );

        \App\Services\NotificationService::notifyRoles(
            [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPER_ADMIN, \App\Enums\UserRole::PROJECT_MANAGER],
            'quotation',
            "Quotation Rejected by Client: {$quotation->reference_number}",
            "Client " . (auth()->user()->name ?? 'Client') . " rejected quotation #{$quotation->reference_number}.",
            route('admin.quotations.show', $quotation->id),
            $quotation
        );

        ActivityLogger::log(
            action: 'quotation.rejected',
            subject: $quotation,
            description: 'Client rejected quotation #' . $quotation->reference_number,
            metadata: [
                'total' => (float) $quotation->total,
                'quotation_reference' => $quotation->reference_number,
            ]
        );

        return redirect()->back()->with('status', 'Quotation has been rejected.');
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    /**
     * Display a listing of client's invoices.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Invoice::class);

        $client = $request->user();

        $query = Invoice::where('client_id', $client->id)
            ->with(['project', 'quotation', 'payments']);

        // Search by reference number or project title
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

        $invoices = $query->orderByDesc('created_at')->paginate(15)->withQueryString();
        $statuses = InvoiceStatus::cases();

        return view('client.invoices.index', compact('invoices', 'statuses'));
    }

    /**
     * Display client invoice detail workspace.
     */
    public function show(Invoice $invoice): View
    {
        Gate::authorize('view', $invoice);

        $invoice->recalculateTotals();
        $invoice->load(['client', 'project', 'quotation', 'milestone', 'payments']);

        $razorpayKeyId = (string) config('services.razorpay.key_id', '');

        return view('client.invoices.show', compact('invoice', 'razorpayKeyId'));
    }
}

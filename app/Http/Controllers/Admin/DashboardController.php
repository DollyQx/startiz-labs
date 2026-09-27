<?php

namespace App\Http\Controllers\Admin;

use App\Enums\InvoiceStatus;
use App\Enums\LeadStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Enums\QuotationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the comprehensive admin CRM dashboard with financial metrics.
     */
    public function index(Request $request): View
    {
        $adminUser = $request->user();

        // Financial Period Filter Handling
        $period = $request->input('period', 'all_time');
        $paymentQuery = Payment::where('status', PaymentStatus::PAID);

        switch ($period) {
            case 'today':
                $paymentQuery->whereDate('paid_at', now()->toDateString());
                break;
            case 'this_week':
                $paymentQuery->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $paymentQuery->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'this_year':
                $paymentQuery->whereBetween('paid_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
            case 'all_time':
            default:
                // No date restriction
                break;
        }

        $totalCollected = (float) $paymentQuery->sum('amount');

        // Current Active Invoice Balances (Authoritative Model Calculations)
        $outstandingBalance = (float) Invoice::whereNotIn('status', [InvoiceStatus::CANCELLED->value])
            ->sum('amount_due');

        $overdueBalance = (float) Invoice::where('status', InvoiceStatus::OVERDUE->value)
            ->orWhere(function ($q) {
                $q->whereNotIn('status', [InvoiceStatus::CANCELLED->value, InvoiceStatus::PAID->value, InvoiceStatus::DRAFT->value])
                  ->whereNotNull('due_date')
                  ->where('due_date', '<', now()->toDateString())
                  ->where('amount_due', '>', 0);
            })->sum('amount_due');

        $unpaidInvoicesCount = Invoice::whereNotIn('status', [InvoiceStatus::CANCELLED->value])
            ->where('amount_paid', 0)
            ->count();

        $stats = [
            'total_clients' => User::where('role', UserRole::CLIENT->value)->count(),
            'active_clients' => User::where('role', UserRole::CLIENT->value)
                ->where('status', UserStatus::ACTIVE->value)
                ->count(),
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('status', LeadStatus::NEW->value)->count(),
            'contacted_leads' => Lead::where('status', LeadStatus::CONTACTED->value)->count(),
            'qualified_leads' => Lead::where('status', LeadStatus::QUALIFIED->value)->count(),
            'quotation_sent_leads' => Lead::where('status', LeadStatus::QUOTATION_SENT->value)->count(),
            'negotiation_leads' => Lead::where('status', LeadStatus::NEGOTIATION->value)->count(),
            'won_leads' => Lead::where('status', LeadStatus::WON->value)->count(),
            'lost_leads' => Lead::where('status', LeadStatus::LOST->value)->count(),
            
            // Project Metrics (Phase 5B)
            'total_projects' => Project::count(),
            'active_projects' => Project::whereIn('status', [
                ProjectStatus::PLANNING->value,
                ProjectStatus::APPROVED->value,
                ProjectStatus::IN_PROGRESS->value,
                ProjectStatus::TESTING->value,
                ProjectStatus::CLIENT_REVIEW->value,
                ProjectStatus::DEPLOYMENT->value,
            ])->count(),
            'completed_projects' => Project::where('status', ProjectStatus::COMPLETED->value)->count(),
            'overdue_projects' => Project::whereNotIn('status', [
                ProjectStatus::COMPLETED->value,
                ProjectStatus::CANCELLED->value,
            ])
            ->whereNotNull('expected_completion_date')
            ->where('expected_completion_date', '<', now()->toDateString())
            ->count(),

            // Quotation Metrics (Phase 6A)
            'total_quotations' => Quotation::count(),
            'draft_quotations' => Quotation::where('status', QuotationStatus::DRAFT->value)->count(),
            'sent_quotations' => Quotation::where('status', QuotationStatus::SENT->value)->count(),
            'accepted_quotations' => Quotation::where('status', QuotationStatus::ACCEPTED->value)->count(),
            'expired_quotations' => Quotation::where('status', QuotationStatus::EXPIRED->value)->count(),

            // Invoice Metrics (Phase 6B & 6D)
            'total_invoices' => Invoice::count(),
            'draft_invoices' => Invoice::where('status', InvoiceStatus::DRAFT->value)->count(),
            'issued_invoices' => Invoice::where('status', InvoiceStatus::ISSUED->value)->count(),
            'paid_invoices' => Invoice::where('status', InvoiceStatus::PAID->value)->count(),
            'partially_paid_invoices' => Invoice::where('status', InvoiceStatus::PARTIALLY_PAID->value)->count(),
            'overdue_invoices' => Invoice::where('status', InvoiceStatus::OVERDUE->value)->count(),
            'unpaid_invoices' => $unpaidInvoicesCount,

            // Financial Metrics (Phase 6D)
            'total_collected' => $totalCollected,
            'outstanding_balance' => $outstandingBalance,
            'overdue_balance' => $overdueBalance,
            'selected_period' => $period,
        ];

        $recentLeads = Lead::with('assignedUser')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentClients = User::where('role', UserRole::CLIENT->value)
            ->with('clientProfile')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentProjects = Project::with('client')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentQuotations = Quotation::with(['client', 'project'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentInvoices = Invoice::with(['client', 'project', 'quotation'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $recentPayments = Payment::with(['client', 'invoice'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'adminUser',
            'stats',
            'recentLeads',
            'recentClients',
            'recentProjects',
            'recentQuotations',
            'recentInvoices',
            'recentPayments'
        ));
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Enums\InvoiceStatus;
use App\Enums\PaymentStatus;
use App\Enums\ProjectStatus;
use App\Enums\QuotationStatus;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the enhanced client dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $profile = $user->clientProfile;

        // Project Summary Counts
        $totalProjects = $user->projects()->count();
        $activeProjects = $user->projects()->whereIn('status', [
            ProjectStatus::PLANNING,
            ProjectStatus::APPROVED,
            ProjectStatus::IN_PROGRESS,
            ProjectStatus::TESTING,
            ProjectStatus::CLIENT_REVIEW,
            ProjectStatus::DEPLOYMENT,
            ProjectStatus::ON_HOLD,
        ])->count();
        $reviewProjectsCount = $user->projects()->where('status', ProjectStatus::CLIENT_REVIEW)->count();
        $completedProjects = $user->projects()->where('status', ProjectStatus::COMPLETED)->count();

        // Commercial Action Item Counts
        $pendingQuotationsCount = $user->quotations()->whereIn('status', [
            QuotationStatus::SENT,
            QuotationStatus::VIEWED,
        ])->count();

        $unpaidInvoicesCount = $user->invoices()->whereIn('status', [
            InvoiceStatus::ISSUED,
            InvoiceStatus::PARTIALLY_PAID,
            InvoiceStatus::OVERDUE,
        ])->count();

        // Support & Notification Summary
        $openTicketsCount = $user->supportTickets()->whereIn('status', [
            TicketStatus::OPEN,
            TicketStatus::IN_PROGRESS,
            TicketStatus::WAITING_FOR_CLIENT,
        ])->count();
        $unreadNotificationsCount = $user->unreadNotifications()->count();

        // Financial Summary Metrics
        $totalInvoiced = (float) $user->invoices()->sum('total');
        $totalPaid = (float) $user->payments()->where('status', PaymentStatus::PAID)->sum('amount');
        $outstandingAmount = (float) $user->invoices()->whereIn('status', [
            InvoiceStatus::ISSUED,
            InvoiceStatus::PARTIALLY_PAID,
            InvoiceStatus::OVERDUE,
        ])->sum('amount_due');

        // Recent Projects with Milestones for progress calculation
        $recentProjects = $user->projects()
            ->with(['service', 'milestones'])
            ->latest()
            ->take(5)
            ->get();

        // Recent safe client-visible activities
        $recentActivities = ActivityLog::forClient($user->id)
            ->clientVisible()
            ->latest('created_at')
            ->take(5)
            ->get();

        return view('client.dashboard', compact(
            'user',
            'profile',
            'totalProjects',
            'activeProjects',
            'reviewProjectsCount',
            'completedProjects',
            'pendingQuotationsCount',
            'unpaidInvoicesCount',
            'openTicketsCount',
            'unreadNotificationsCount',
            'totalInvoiced',
            'totalPaid',
            'outstandingAmount',
            'recentProjects',
            'recentActivities'
        ));
    }
}

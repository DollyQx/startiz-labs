<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminSupportTicketController extends Controller
{
    /**
     * Display a listing of support tickets for admin/support staff.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', SupportTicket::class);

        $query = SupportTicket::query()
            ->with(['client', 'project', 'assignedTo'])
            ->latest('updated_at');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', $search)
                    ->orWhere('reference_number', 'like', $search);
            });
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('assigned_to_id')) {
            if ($request->assigned_to_id === 'unassigned') {
                $query->whereNull('assigned_to_id');
            } else {
                $query->where('assigned_to_id', $request->assigned_to_id);
            }
        }

        $tickets = $query->paginate(15)->withQueryString();

        $clients = User::where('role', UserRole::CLIENT->value)
            ->orderBy('name', 'asc')
            ->get();

        $staffUsers = User::whereIn('role', [
            UserRole::SUPER_ADMIN->value,
            UserRole::ADMIN->value,
            UserRole::SUPPORT->value,
            UserRole::PROJECT_MANAGER->value,
            UserRole::DEVELOPER->value,
        ])->orderBy('name', 'asc')->get();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'clients' => $clients,
            'staffUsers' => $staffUsers,
            'statuses' => TicketStatus::cases(),
            'priorities' => TicketPriority::cases(),
            'categories' => TicketCategory::cases(),
        ]);
    }

    /**
     * Display the specified ticket workspace for staff.
     */
    public function show(SupportTicket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load([
            'client',
            'project',
            'assignedTo',
            'messages.sender',
        ]);

        $staffUsers = User::whereIn('role', [
            UserRole::SUPER_ADMIN->value,
            UserRole::ADMIN->value,
            UserRole::SUPPORT->value,
            UserRole::PROJECT_MANAGER->value,
            UserRole::DEVELOPER->value,
        ])->orderBy('name', 'asc')->get();

        return view('admin.tickets.show', [
            'ticket' => $ticket,
            'staffUsers' => $staffUsers,
            'statuses' => TicketStatus::cases(),
            'priorities' => TicketPriority::cases(),
        ]);
    }

    /**
     * Post a staff reply to client.
     */
    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('reply', $ticket);

        $validated = $request->validate([
            'message' => ['required', 'string'],
            'status' => ['nullable', Rule::enum(TicketStatus::class)],
        ]);

        $user = $request->user();

        // Create public reply
        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        // Update status if provided or default to WAITING_FOR_CLIENT
        $newStatus = $validated['status'] ?? TicketStatus::WAITING_FOR_CLIENT->value;
        $ticket->update(['status' => $newStatus]);

        ActivityLogger::log('ticket.replied', $ticket, "Staff replied to ticket {$ticket->reference_number}.");

        if ($ticket->client) {
            \App\Services\NotificationService::notifyUser(
                $ticket->client,
                'support_ticket',
                "Support Ticket Reply: #{$ticket->reference_number}",
                "Staff replied to your support ticket: {$ticket->subject}",
                route('client.tickets.show', $ticket->id),
                $ticket
            );
        }

        return back()->with('status', 'Response sent to client successfully.');
    }

    /**
     * Add an internal note (staff only).
     */
    public function internalNote(Request $request, SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('addInternalNote', $ticket);

        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $user = $request->user();

        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'message' => $validated['message'],
            'is_internal' => true,
        ]);

        ActivityLogger::log('ticket.internal_note_added', $ticket, "Internal note added to ticket {$ticket->reference_number}.");

        return back()->with('status', 'Internal note added successfully.');
    }

    /**
     * Update ticket status.
     */
    public function updateStatus(Request $request, SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('updateStatus', $ticket);

        $validated = $request->validate([
            'status' => ['required', Rule::enum(TicketStatus::class)],
        ]);

        $newStatus = $validated['status'];
        $updateData = ['status' => $newStatus];

        if ($newStatus === TicketStatus::RESOLVED->value) {
            $updateData['resolved_at'] = now();
        } elseif ($newStatus === TicketStatus::CLOSED->value) {
            $updateData['closed_at'] = now();
            if (empty($ticket->resolved_at)) {
                $updateData['resolved_at'] = now();
            }
        } elseif (in_array($newStatus, [TicketStatus::OPEN->value, TicketStatus::IN_PROGRESS->value], true)) {
            $updateData['closed_at'] = null;
        }

        $ticket->update($updateData);

        $action = match ($newStatus) {
            TicketStatus::RESOLVED->value => 'ticket.resolved',
            TicketStatus::CLOSED->value => 'ticket.closed',
            default => 'ticket.status_updated',
        };

        ActivityLogger::log($action, $ticket, "Ticket {$ticket->reference_number} status updated to " . ucfirst(str_replace('_', ' ', $newStatus)));

        if ($ticket->client) {
            \App\Services\NotificationService::notifyUser(
                $ticket->client,
                'support_ticket',
                "Ticket Status Updated: #{$ticket->reference_number}",
                "Your ticket status has been updated to " . ucfirst(str_replace('_', ' ', $newStatus)) . ".",
                route('client.tickets.show', $ticket->id),
                $ticket
            );
        }

        return back()->with('status', 'Ticket status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)) . '.');
    }

    /**
     * Assign ticket to staff member.
     */
    public function assign(Request $request, SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('assign', $ticket);

        $validated = $request->validate([
            'assigned_to_id' => ['nullable', 'exists:users,id'],
        ]);

        $ticket->update([
            'assigned_to_id' => $validated['assigned_to_id'] ?? null,
        ]);

        $assigneeName = $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned';

        ActivityLogger::log('ticket.assigned', $ticket, "Ticket {$ticket->reference_number} assigned to {$assigneeName}.");

        if ($ticket->assignedTo) {
            \App\Services\NotificationService::notifyUser(
                $ticket->assignedTo,
                'support_ticket',
                "Support Ticket Assigned: #{$ticket->reference_number}",
                "Support ticket #{$ticket->reference_number} has been assigned to you.",
                route('admin.tickets.show', $ticket->id),
                $ticket
            );
        }

        return back()->with('status', "Ticket assigned to {$assigneeName}.");
    }
}

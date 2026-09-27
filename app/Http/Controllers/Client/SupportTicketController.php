<?php

namespace App\Http\Controllers\Client;

use App\Enums\TicketCategory;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\SupportTicket;
use App\Models\SupportTicketMessage;
use App\Services\ActivityLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SupportTicketController extends Controller
{
    /**
     * Display a listing of the client's support tickets.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', SupportTicket::class);

        $user = $request->user();

        $query = SupportTicket::query()
            ->where('client_id', $user->id)
            ->with(['project'])
            ->latest('updated_at');

        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', $search)
                    ->orWhere('reference_number', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->paginate(10)->withQueryString();

        return view('client.tickets.index', [
            'tickets' => $tickets,
            'statuses' => TicketStatus::cases(),
            'priorities' => TicketPriority::cases(),
            'categories' => TicketCategory::cases(),
        ]);
    }

    /**
     * Show the form for creating a new support ticket.
     */
    public function create(Request $request): View
    {
        Gate::authorize('create', SupportTicket::class);

        $projects = Project::where('client_id', $request->user()->id)
            ->orderBy('title', 'asc')
            ->get();

        return view('client.tickets.create', [
            'projects' => $projects,
            'categories' => TicketCategory::cases(),
            'priorities' => TicketPriority::cases(),
        ]);
    }

    /**
     * Store a newly created support ticket in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', SupportTicket::class);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', Rule::enum(TicketCategory::class)],
            'priority' => ['required', Rule::enum(TicketPriority::class)],
            'project_id' => ['nullable', 'exists:projects,id'],
        ]);

        $user = $request->user();

        // Strict Server-Side Project Ownership Validation
        if (! empty($validated['project_id'])) {
            $project = Project::find($validated['project_id']);
            if (! $project || (int) $project->client_id !== (int) $user->id) {
                return back()->withErrors(['project_id' => 'The selected project does not belong to your account.'])->withInput();
            }
        }

        $ticket = SupportTicket::create([
            'client_id' => $user->id,
            'project_id' => $validated['project_id'] ?? null,
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'priority' => $validated['priority'],
            'status' => TicketStatus::OPEN,
        ]);

        // Create initial public message
        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'message' => $validated['description'],
            'is_internal' => false,
        ]);

        ActivityLogger::log('ticket.created', $ticket, "Support ticket {$ticket->reference_number} submitted.");

        \App\Services\NotificationService::notifyRoles(
            [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPER_ADMIN, \App\Enums\UserRole::SUPPORT],
            'support_ticket',
            "New Support Ticket #{$ticket->reference_number}",
            "Client {$user->name} submitted ticket: {$ticket->subject}",
            route('admin.tickets.show', $ticket->id),
            $ticket
        );

        return redirect()->route('client.tickets.show', $ticket->id)
            ->with('status', 'Support ticket submitted successfully! Reference: ' . $ticket->reference_number);
    }

    /**
     * Display the specified support ticket workspace.
     */
    public function show(SupportTicket $ticket): View
    {
        Gate::authorize('view', $ticket);

        $ticket->load([
            'project',
            'clientVisibleMessages.sender',
        ]);

        return view('client.tickets.show', [
            'ticket' => $ticket,
        ]);
    }

    /**
     * Client reply to support ticket.
     */
    public function reply(Request $request, SupportTicket $ticket): RedirectResponse
    {
        Gate::authorize('reply', $ticket);

        $validated = $request->validate([
            'message' => ['required', 'string'],
        ]);

        $user = $request->user();

        // Create message strictly as public (is_internal = false)
        SupportTicketMessage::create([
            'ticket_id' => $ticket->id,
            'sender_id' => $user->id,
            'message' => $validated['message'],
            'is_internal' => false,
        ]);

        // Reopen or transition status if ticket was awaiting client
        if (in_array($ticket->status, [TicketStatus::WAITING_FOR_CLIENT, TicketStatus::RESOLVED], true)) {
            $ticket->update(['status' => TicketStatus::IN_PROGRESS]);
        }

        ActivityLogger::log('ticket.replied', $ticket, "Client replied to support ticket {$ticket->reference_number}.");

        \App\Services\NotificationService::notifyRoles(
            [\App\Enums\UserRole::ADMIN, \App\Enums\UserRole::SUPER_ADMIN, \App\Enums\UserRole::SUPPORT],
            'support_ticket',
            "Client Reply on Ticket #{$ticket->reference_number}",
            "Client {$user->name} replied to support ticket {$ticket->reference_number}.",
            route('admin.tickets.show', $ticket->id),
            $ticket
        );

        return redirect()->route('client.tickets.show', $ticket->id)
            ->with('status', 'Reply posted successfully.');
    }
}

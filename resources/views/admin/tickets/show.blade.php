@extends('layouts.admin')

@section('title', 'Manage Ticket: ' . $ticket->reference_number)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Top Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.tickets.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Ticket Directory
        </a>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Quick Status Change Form -->
            <form method="POST" action="{{ route('admin.tickets.status', $ticket->id) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm">
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ $ticket->status->value === $st->value ? 'selected' : '' }}>
                            Status: {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </form>

            <!-- Quick Assignee Form -->
            <form method="POST" action="{{ route('admin.tickets.assign', $ticket->id) }}" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="assigned_to_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 shadow-sm">
                    <option value="">Unassigned</option>
                    @foreach($staffUsers as $staff)
                        <option value="{{ $staff->id }}" {{ $ticket->assigned_to_id == $staff->id ? 'selected' : '' }}>
                            Assigned: {{ $staff->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <!-- Status Alert Banner -->
    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between">
            <span>{{ session('status') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">&times;</button>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Ticket Workspace & Conversation -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Ticket Header Info -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-4 flex items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-blue-600 uppercase tracking-wide">{{ $ticket->reference_number }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $ticket->status->badgeClass() }}">
                                {{ $ticket->status->label() }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $ticket->priority->badgeClass() }}">
                                {{ $ticket->priority->label() }}
                            </span>
                        </div>
                        <h1 class="text-xl font-extrabold text-slate-900 tracking-tight mt-1.5">{{ $ticket->subject }}</h1>
                    </div>
                </div>

                <div class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100 whitespace-pre-line">
                    {{ $ticket->description }}
                </div>
            </div>

            <!-- Conversation Timeline (Includes Public Replies & Internal Notes) -->
            <div class="space-y-4">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider">Full Conversation & Activity Log</h2>

                <div class="space-y-4">
                    @forelse($ticket->messages as $msg)
                        @php
                            $isInternal = $msg->is_internal;
                            $isStaffSender = $msg->sender?->isSupportStaff();
                        @endphp
                        <div class="p-4 rounded-2xl border shadow-sm space-y-2 {{ $isInternal ? 'bg-amber-50/60 border-amber-200' : 'bg-white border-slate-200/90' }}">
                            <div class="flex items-center justify-between text-xs border-b border-slate-100/80 pb-2">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900">{{ $msg->sender?->name ?? 'User' }}</span>
                                    @if($isInternal)
                                        <span class="bg-amber-100 text-amber-900 border border-amber-300 font-extrabold text-[10px] px-2 py-0.5 rounded-full uppercase flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                            Internal Staff Note (Confidential)
                                        </span>
                                    @elseif($isStaffSender)
                                        <span class="bg-blue-100 text-blue-800 font-extrabold text-[10px] px-2 py-0.5 rounded-full uppercase">Staff Public Reply</span>
                                    @else
                                        <span class="bg-slate-100 text-slate-600 font-bold text-[10px] px-2 py-0.5 rounded-full uppercase">Client Reply</span>
                                    @endif
                                </div>
                                <span class="text-slate-400 text-[11px]">{{ $msg->created_at->format('M d, Y h:i A') }}</span>
                            </div>

                            <div class="text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                                {{ $msg->message }}
                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-6 rounded-2xl border border-slate-200 text-center text-slate-400 text-xs">
                            No messages recorded for this ticket yet.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Action Forms (Public Reply & Internal Note) -->
            <div class="space-y-6">

                <!-- 1. Public Reply Form -->
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                            Reply to Client
                        </h3>
                        <span class="text-xs text-slate-400">Visible to client on their portal</span>
                    </div>

                    <form method="POST" action="{{ route('admin.tickets.reply', $ticket->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <textarea name="message" rows="4" required placeholder="Type public response to the client..." class="w-full p-4 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all"></textarea>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center gap-2">
                                <label for="reply_status" class="text-xs font-bold text-slate-600">Update Status To:</label>
                                <select id="reply_status" name="status" class="px-3 py-1.5 rounded-xl border border-slate-200 text-xs font-bold bg-white text-slate-700">
                                    <option value="waiting_for_client" selected>Waiting for Client</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="resolved">Resolved</option>
                                </select>
                            </div>

                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                                Send Client Reply
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. Internal Staff Note Form -->
                <div class="bg-amber-50/40 p-6 rounded-2xl border border-amber-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-amber-200/60 pb-3">
                        <h3 class="text-sm font-bold text-amber-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Add Internal Note
                        </h3>
                        <span class="text-xs font-bold text-amber-700">Strictly confidential (hidden from client)</span>
                    </div>

                    <form method="POST" action="{{ route('admin.tickets.internal-note', $ticket->id) }}" class="space-y-4">
                        @csrf
                        <div>
                            <textarea name="message" rows="3" required placeholder="Add technical notes, internal investigation results, or handoff instructions..." class="w-full p-4 rounded-xl border border-amber-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition-all bg-white"></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <p class="text-xs text-amber-800">Only visible to administrators and support staff.</p>
                            <button type="submit" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md shadow-amber-500/20 transition-all">
                                Post Internal Note
                            </button>
                        </div>
                    </form>
                </div>

            </div>

        </div>

        <!-- Right Col: Metadata & Quick Details -->
        <div class="space-y-6">

            <!-- Client Info Card -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-3">Client Details</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block">Client Name</span>
                        <a href="{{ route('admin.clients.show', $ticket->client_id) }}" class="font-bold text-blue-600 hover:underline text-sm">
                            {{ $ticket->client?->name ?? 'N/A' }}
                        </a>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Email Address</span>
                        <span class="font-medium text-slate-800">{{ $ticket->client?->email }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Phone</span>
                        <span class="font-medium text-slate-800">{{ $ticket->client?->phone ?? 'Not provided' }}</span>
                    </div>
                </div>
            </div>

            <!-- Project Details Card (If any) -->
            @if($ticket->project)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-3">Associated Project</h3>

                    <div class="space-y-3 text-xs">
                        <div>
                            <span class="text-slate-400 block">Project Title</span>
                            <a href="{{ route('admin.projects.show', $ticket->project->id) }}" class="font-bold text-blue-600 hover:underline text-sm">
                                {{ $ticket->project->title }}
                            </a>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Reference Number</span>
                            <span class="font-mono font-bold text-slate-700">{{ $ticket->project->reference_number }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block">Project Status</span>
                            <span class="font-bold text-slate-700 uppercase">{{ $ticket->project->status->value }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ticket Audit Info -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
                <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-3">Ticket Information</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block">Ticket Category</span>
                        <span class="font-bold text-slate-800 capitalize">{{ $ticket->category ? $ticket->category->label() : ucfirst($ticket->category) }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Created At</span>
                        <span class="font-medium text-slate-800">{{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Resolved At</span>
                        <span class="font-medium text-slate-800">{{ $ticket->resolved_at ? $ticket->resolved_at->format('M d, Y h:i A') : 'N/A' }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Closed At</span>
                        <span class="font-medium text-slate-800">{{ $ticket->closed_at ? $ticket->closed_at->format('M d, Y h:i A') : 'N/A' }}</span>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection

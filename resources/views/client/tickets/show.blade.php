@extends('layouts.client')

@section('title', 'Ticket Workspace: ' . $ticket->reference_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('client.tickets.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Support Tickets
        </a>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $ticket->status->badgeClass() }}">
                {{ $ticket->status->label() }}
            </span>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $ticket->priority->badgeClass() }}">
                {{ $ticket->priority->label() }} Priority
            </span>
        </div>
    </div>

    <!-- Ticket Summary Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs font-mono font-bold text-blue-600 uppercase tracking-wide">{{ $ticket->reference_number }}</span>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $ticket->subject }}</h1>
            </div>
            <div class="text-right sm:text-right">
                <span class="text-xs text-slate-400 block">Submitted {{ $ticket->created_at->format('M d, Y h:i A') }}</span>
                @if($ticket->project)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-slate-600 bg-slate-100 px-2.5 py-1 rounded-lg mt-1">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        Project: {{ $ticket->project->title }}
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs">
            <div>
                <span class="text-slate-400 block">Category</span>
                <span class="font-bold text-slate-700 capitalize">{{ $ticket->category ? $ticket->category->label() : ucfirst($ticket->category) }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Assigned Staff</span>
                <span class="font-bold text-slate-700">{{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Unassigned (Queue)' }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Last Updated</span>
                <span class="font-bold text-slate-700">{{ $ticket->updated_at->diffForHumans() }}</span>
            </div>
            <div>
                <span class="text-slate-400 block">Resolution Status</span>
                <span class="font-bold text-slate-700">{{ $ticket->resolved_at ? 'Resolved ' . $ticket->resolved_at->format('M d, Y') : 'In Progress' }}</span>
            </div>
        </div>
    </div>

    <!-- Status Alert Banner -->
    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
            {{ session('status') }}
        </div>
    @endif

    <!-- Conversation Message Timeline -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-slate-700 uppercase tracking-wider">Conversation History</h2>

        <div class="space-y-4">
            @forelse($ticket->clientVisibleMessages as $msg)
                @php
                    $isMe = (int)$msg->sender_id === (int)auth()->id();
                @endphp
                <div class="flex gap-4 {{ $isMe ? 'flex-row-reverse' : '' }}">
                    <!-- Sender Avatar -->
                    <div class="w-10 h-10 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-xs {{ $isMe ? 'bg-blue-600 text-white' : 'bg-slate-800 text-amber-400' }}">
                        {{ strtoupper(substr($msg->sender?->name ?? 'U', 0, 2)) }}
                    </div>

                    <!-- Message Bubble -->
                    <div class="max-w-2xl space-y-1">
                        <div class="flex items-center gap-2 text-xs {{ $isMe ? 'justify-end' : '' }}">
                            <span class="font-bold text-slate-800">{{ $msg->sender?->name ?? 'User' }}</span>
                            @if(!$isMe)
                                <span class="bg-blue-100 text-blue-800 font-extrabold text-[10px] px-2 py-0.5 rounded-full uppercase">Support Staff</span>
                            @else
                                <span class="bg-slate-100 text-slate-600 font-bold text-[10px] px-2 py-0.5 rounded-full">Client</span>
                            @endif
                            <span class="text-slate-400 text-[11px]">{{ $msg->created_at->format('M d, g:i A') }}</span>
                        </div>

                        <div class="p-4 rounded-2xl text-sm leading-relaxed {{ $isMe ? 'bg-blue-600 text-white rounded-tr-none' : 'bg-white border border-slate-200 text-slate-800 rounded-tl-none shadow-sm' }}">
                            {!! nl2br(e($msg->message)) !!}
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white p-6 rounded-2xl border border-slate-200 text-center text-slate-400 text-xs">
                    No conversation messages found.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Reply Form Box -->
    @if($ticket->status->value !== \App\Enums\TicketStatus::CLOSED->value)
        <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
            <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Post Reply</h3>

            <form method="POST" action="{{ route('client.tickets.reply', $ticket->id) }}" class="space-y-4">
                @csrf
                <div>
                    <textarea name="message" rows="4" required placeholder="Type your response to the support team..." class="w-full p-4 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all"></textarea>
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-400">Posting a reply will reopen or set the ticket status to In Progress.</p>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 text-slate-600 text-xs font-semibold text-center">
            This support ticket has been closed. Contact support or create a new ticket if you need further assistance.
        </div>
    @endif

</div>
@endsection

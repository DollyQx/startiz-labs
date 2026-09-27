@extends('layouts.admin')

@section('title', 'Support Ticket Management')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Support Ticket Management</h1>
            <p class="text-sm text-slate-500 mt-1">Manage client support inquiries, assign staff, reply to issues, and track resolutions.</p>
        </div>
    </div>

    <!-- Multi-Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.tickets.index') }}" class="grid grid-cols-1 sm:grid-cols-6 gap-3">
            <!-- Search -->
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ref # or subject..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Client Filter -->
            <div>
                <select name="client_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <select name="priority" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                    <option value="">All Priorities</option>
                    @foreach($priorities as $pr)
                        <option value="{{ $pr->value }}" {{ request('priority') === $pr->value ? 'selected' : '' }}>
                            {{ $pr->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Assignee Filter & Clear -->
            <div class="flex gap-2">
                <select name="assigned_to_id" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                    <option value="">All Staff</option>
                    <option value="unassigned" {{ request('assigned_to_id') === 'unassigned' ? 'selected' : '' }}>Unassigned</option>
                    @foreach($staffUsers as $staff)
                        <option value="{{ $staff->id }}" {{ request('assigned_to_id') == $staff->id ? 'selected' : '' }}>
                            {{ $staff->name }}
                        </option>
                    @endforeach
                </select>

                @if(request()->anyFilled(['search', 'client_id', 'status', 'priority', 'category', 'assigned_to_id']))
                    <a href="{{ route('admin.tickets.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1 transition-all" title="Clear Filters">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Admin Tickets Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Reference</th>
                        <th class="py-3.5 px-6">Client</th>
                        <th class="py-3.5 px-6">Subject & Project</th>
                        <th class="py-3.5 px-6 text-center">Priority</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6">Assigned To</th>
                        <th class="py-3.5 px-6">Last Updated</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($tickets as $ticket)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-blue-600">
                                {{ $ticket->reference_number }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $ticket->client?->name ?? 'N/A' }}</span>
                                <span class="text-xs text-slate-400 block">{{ $ticket->client?->email }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors block">
                                    {{ $ticket->subject }}
                                </a>
                                @if($ticket->project)
                                    <span class="text-xs text-slate-400 block font-mono">
                                        Project: {{ $ticket->project->title }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $ticket->priority->badgeClass() }}">
                                    {{ $ticket->priority->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $ticket->status->badgeClass() }}">
                                    {{ $ticket->status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-700">
                                @if($ticket->assignedTo)
                                    <span class="font-bold text-slate-800">{{ $ticket->assignedTo->name }}</span>
                                @else
                                    <span class="text-amber-600 font-bold bg-amber-50 px-2 py-0.5 rounded">Unassigned</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $ticket->updated_at->diffForHumans() }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    <span>Manage</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                                No support tickets match the current filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tickets->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

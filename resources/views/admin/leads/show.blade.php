@extends('layouts.admin')

@section('title', 'Lead: ' . $lead->reference_number)
@section('breadcrumb', 'Leads / ' . $lead->reference_number)

@section('content')
<div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-2">
                &larr; Back to Leads Pipeline
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-mono">{{ $lead->reference_number }}</h1>
                <span class="inline-flex px-3 py-0.5 text-xs font-bold rounded-full border
                    {{ $lead->status->value === 'won' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                    {{ $lead->status->value === 'lost' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                    {{ $lead->status->value === 'new' ? 'bg-slate-100 text-slate-800 border-slate-200' : '' }}
                    {{ in_array($lead->status->value, ['contacted', 'qualified', 'quotation_sent', 'negotiation']) ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                ">
                    {{ $lead->status->label() }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1">{{ $lead->name }} — {{ $lead->company_name ?? 'Individual' }}</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.leads.edit', $lead->id) }}" class="btn-base btn-outline btn-sm">
                Edit Lead Details
            </a>
        </div>
    </div>

    <!-- Pipeline Stage Selector Card -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Update Pipeline Stage</h3>
        
        <form action="{{ route('admin.leads.status', $lead->id) }}" method="POST" class="flex flex-wrap items-center gap-2">
            @csrf
            @method('PATCH')
            
            @foreach($statuses as $st)
                <button type="submit" name="status" value="{{ $st->value }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border {{ $lead->status->value === $st->value ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-300' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    {{ $st->label() }}
                </button>
            @endforeach
        </form>
    </div>

    <!-- Main Detail Grid (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Lead Info (2/3 width) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact & Company Overview -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Contact & Business Profile
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-500 font-semibold uppercase block">Contact Name</span>
                        <span class="font-medium text-slate-900">{{ $lead->name }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-semibold uppercase block">Email Address</span>
                        <a href="mailto:{{ $lead->email }}" class="font-medium text-blue-600 hover:underline">{{ $lead->email }}</a>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-semibold uppercase block">Phone Number</span>
                        <span class="font-medium text-slate-900">{{ $lead->phone ?? 'Not provided' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-semibold uppercase block">Company Name</span>
                        <span class="font-medium text-slate-900">{{ $lead->company_name ?? 'Individual' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-semibold uppercase block">Inquiry Source</span>
                        <span class="font-medium text-slate-900 capitalize">{{ $lead->source ?? 'Website' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-500 font-semibold uppercase block">Inquiry Date</span>
                        <span class="font-medium text-slate-900">{{ $lead->created_at->format('M d, Y H:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Requirement Notes & Scope -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Project Scope & Requirement Notes
                </h3>
                <div class="text-sm text-slate-700 whitespace-pre-wrap leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-200">
                    {{ $lead->notes ?? 'No requirement notes recorded for this lead.' }}
                </div>
            </div>

            <!-- Activity Audit Trail -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-200 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Activity Audit Timeline
                </h3>

                <div class="space-y-4">
                    @forelse($activityLogs as $log)
                        <div class="flex gap-3 text-xs border-l-2 border-blue-500 pl-3 py-1">
                            <div>
                                <p class="font-bold text-slate-900">{{ $log->description ?? $log->action }}</p>
                                <p class="text-slate-500 mt-0.5">
                                    By <span class="font-semibold text-slate-700">{{ $log->actor->name ?? 'System' }}</span> &bull; {{ $log->created_at->format('M d, Y H:i A') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic">No activity logged for this lead yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right Column: Assignments & Relations (1/3 width) -->
        <div class="space-y-6">
            <!-- Staff Assignment Card -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Assigned Account Representative</h3>
                @if($lead->assignedUser)
                    <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs">
                            {{ strtoupper(substr($lead->assignedUser->name, 0, 1)) }}
                        </div>
                        <div class="text-xs">
                            <p class="font-bold text-slate-900">{{ $lead->assignedUser->name }}</p>
                            <p class="text-slate-500">{{ $lead->assignedUser->email }}</p>
                        </div>
                    </div>
                @else
                    <div class="p-3 bg-amber-50 rounded-lg border border-amber-200 text-xs text-amber-800">
                        Lead is currently unassigned.
                    </div>
                @endif
            </div>

            <!-- Linked Client Account Card -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500">Linked Client Account</h3>
                @if($lead->client)
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs space-y-1">
                        <p class="font-bold text-slate-900">{{ $lead->client->name }}</p>
                        <p class="text-slate-500">{{ $lead->client->email }}</p>
                        <div class="pt-2">
                            <a href="{{ route('admin.clients.show', $lead->client->id) }}" class="text-blue-600 hover:underline font-semibold">
                                View Full Client Profile &rarr;
                            </a>
                        </div>
                    </div>
                @else
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs text-slate-500 italic">
                        No registered client account linked to this standalone lead.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

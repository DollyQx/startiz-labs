@extends('layouts.admin')

@section('title', 'Manage Change Request ' . $changeRequest->reference_number)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    <!-- Top Navigation & Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <a href="{{ route('admin.change-requests.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Change Requests Directory
        </a>

        <!-- Status Control Actions -->
        <div class="flex items-center gap-3">
            @if(in_array($changeRequest->status, [\App\Enums\ChangeRequestStatus::PENDING, \App\Enums\ChangeRequestStatus::UNDER_REVIEW]))
                <form method="POST" action="{{ route('admin.change-requests.reject', $changeRequest->id) }}" onsubmit="return confirm('Are you sure you want to REJECT this change request?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition-all">
                        Reject Request
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.change-requests.approve', $changeRequest->id) }}" onsubmit="return confirm('Are you sure you want to APPROVE this change request?');">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all">
                        Approve Change Request
                    </button>
                </form>
            @else
                <span class="text-xs font-bold text-slate-500 italic">
                    Status locked ({{ $changeRequest->status->label() }})
                </span>
            @endif
        </div>
    </div>

    <!-- Main Workspace Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Assessment Form & Scope Details -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Request Details Card -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                                {{ $changeRequest->reference_number }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $changeRequest->priority->badgeClass() }}">
                                {{ $changeRequest->priority->label() }} Priority
                            </span>
                        </div>
                        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $changeRequest->title }}</h1>
                    </div>

                    <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-extrabold border {{ $changeRequest->status->badgeClass() }}">
                        {{ $changeRequest->status->label() }}
                    </span>
                </div>

                @if($changeRequest->reason)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Client Business Justification</span>
                        <p class="text-sm font-medium text-slate-700">{{ $changeRequest->reason }}</p>
                    </div>
                @endif

                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Requested Scope Description</span>
                    <div class="p-4 rounded-xl bg-slate-50/50 border border-slate-100 text-slate-800 text-sm whitespace-pre-line leading-relaxed">
                        {{ $changeRequest->description }}
                    </div>
                </div>
            </div>

            <!-- Admin Scope & Financial Assessment Form -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4">
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Technical & Financial Scope Assessment</h2>
                    <p class="text-xs text-slate-500">Provide cost estimates, timeline impacts, and client feedback prior to final approval.</p>
                </div>

                <form method="POST" action="{{ route('admin.change-requests.review', $changeRequest->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Est Cost & Est Days Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="estimated_cost" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Estimated Additional Cost (₹)</label>
                            <input type="number" step="0.01" min="0" id="estimated_cost" name="estimated_cost" value="{{ old('estimated_cost', $changeRequest->estimated_cost) }}" placeholder="e.g. 25000.00" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                        </div>

                        <div>
                            <label for="estimated_days" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Estimated Additional Days</label>
                            <input type="number" min="0" id="estimated_days" name="estimated_days" value="{{ old('estimated_days', $changeRequest->estimated_days) }}" placeholder="e.g. 5" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                        </div>
                    </div>

                    <!-- Scope Impact Analysis -->
                    <div>
                        <label for="scope_impact" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Scope & Architectural Impact Analysis</label>
                        <textarea id="scope_impact" name="scope_impact" rows="3" placeholder="Detail technical changes, database modifications, or dependencies impacted..." class="w-full p-4 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">{{ old('scope_impact', $changeRequest->scope_impact) }}</textarea>
                    </div>

                    <!-- Client Visible Feedback Notes -->
                    <div>
                        <label for="client_notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Client-Visible Feedback & Notes</label>
                        <textarea id="client_notes" name="client_notes" rows="3" placeholder="Commercial notes or terms visible to the client on their portal..." class="w-full p-4 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">{{ old('client_notes', $changeRequest->client_notes) }}</textarea>
                    </div>

                    <!-- Confidential Admin Internal Notes -->
                    <div class="p-5 rounded-2xl bg-amber-50/50 border border-amber-200/80 space-y-2">
                        <label for="admin_notes" class="block text-xs font-bold text-amber-900 uppercase tracking-wider flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Internal Staff Notes <span class="text-amber-700 font-normal font-mono text-xs">(CONFIDENTIAL - HIDDEN FROM CLIENT)</span>
                        </label>
                        <textarea id="admin_notes" name="admin_notes" rows="3" placeholder="Enter private staff observations, engineering risk assessments, or internal comments..." class="w-full p-3.5 rounded-xl border border-amber-200 text-sm bg-white text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-600 transition-all">{{ old('admin_notes', $changeRequest->admin_notes) }}</textarea>
                    </div>

                    <div class="flex items-center justify-end pt-2">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                            Save Assessment & Notes
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Right 1 Col: Metadata Sidebar Card -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-3">Client & Project Metadata</h3>

                <div>
                    <span class="text-xs text-slate-400 block font-medium">Client Account</span>
                    @if($changeRequest->client)
                        <a href="{{ route('admin.clients.show', $changeRequest->client->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors text-sm block">
                            {{ $changeRequest->client->name }}
                        </a>
                        <span class="text-xs font-mono text-slate-400">{{ $changeRequest->client->email }}</span>
                    @else
                        <span class="text-sm font-bold text-slate-900">N/A</span>
                    @endif
                </div>

                <div class="pt-2 border-t border-slate-100">
                    <span class="text-xs text-slate-400 block font-medium">Target Project</span>
                    @if($changeRequest->project)
                        <a href="{{ route('admin.projects.show', $changeRequest->project->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors text-sm block">
                            {{ $changeRequest->project->title }}
                        </a>
                        <span class="text-xs font-mono text-slate-400">{{ $changeRequest->project->reference_number }}</span>
                    @else
                        <span class="text-sm font-bold text-slate-900">N/A</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Submitted</span>
                        <span class="text-xs font-bold text-slate-700 font-mono">{{ $changeRequest->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Evaluator</span>
                        <span class="text-xs font-bold text-slate-700 font-mono">{{ $changeRequest->reviewer->name ?? 'Unassigned' }}</span>
                    </div>
                </div>

                @if($changeRequest->approved_at)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-xs text-emerald-600 block font-bold">Approved On</span>
                        <span class="text-xs font-mono font-bold text-slate-700">{{ $changeRequest->approved_at->format('M d, Y H:i') }}</span>
                    </div>
                @endif

                @if($changeRequest->rejected_at)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-xs text-rose-600 block font-bold">Rejected On</span>
                        <span class="text-xs font-mono font-bold text-slate-700">{{ $changeRequest->rejected_at->format('M d, Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection

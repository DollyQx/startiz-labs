@extends('layouts.client')

@section('title', 'Change Request ' . $changeRequest->reference_number)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('client.change-requests.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Directory
        </a>

        @if($changeRequest->status->isCancellable())
            <form method="POST" action="{{ route('client.change-requests.cancel', $changeRequest->id) }}" onsubmit="return confirm('Are you sure you want to cancel this pending change request?');">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-600 hover:text-rose-700 border border-slate-200 hover:border-rose-200 font-bold text-xs transition-all">
                    Cancel Change Request
                </button>
            </form>
        @endif
    </div>

    <!-- Main Workspace Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Details & Admin Assessment -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Primary Request Info Card -->
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

                <!-- Business Reason if present -->
                @if($changeRequest->reason)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Business Reason / Justification</span>
                        <p class="text-sm font-medium text-slate-700">{{ $changeRequest->reason }}</p>
                    </div>
                @endif

                <!-- Detailed Description -->
                <div>
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Scope Description</span>
                    <div class="p-4 rounded-xl bg-slate-50/50 border border-slate-100 text-slate-800 text-sm whitespace-pre-line leading-relaxed font-normal">
                        {{ $changeRequest->description }}
                    </div>
                </div>
            </div>

            <!-- Scope & Cost Review Assessment Card (From Admin) -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
                <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Scope & Financial Assessment</h2>
                        <p class="text-xs text-slate-500">Engineering and commercial evaluation provided by Startiz Labs management.</p>
                    </div>
                    @if($changeRequest->reviewed_at)
                        <span class="text-xs text-slate-400 font-mono">Reviewed {{ $changeRequest->reviewed_at->format('M d, Y') }}</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Estimated Additional Cost</span>
                        <span class="text-xl font-extrabold font-mono text-slate-900">
                            {{ $changeRequest->estimated_cost !== null ? '₹' . number_format($changeRequest->estimated_cost, 2) : 'Under Assessment' }}
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/60">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Estimated Schedule Impact</span>
                        <span class="text-xl font-extrabold font-mono text-slate-900">
                            {{ $changeRequest->estimated_days !== null ? '+' . $changeRequest->estimated_days . ' Working Days' : 'Under Assessment' }}
                        </span>
                    </div>
                </div>

                @if($changeRequest->scope_impact)
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Technical & Scope Impact Analysis</span>
                        <div class="p-4 rounded-xl bg-blue-50/40 border border-blue-100 text-slate-800 text-sm leading-relaxed">
                            {{ $changeRequest->scope_impact }}
                        </div>
                    </div>
                @endif

                @if($changeRequest->client_notes)
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Feedback & Commercial Notes</span>
                        <div class="p-4 rounded-xl bg-amber-50/40 border border-amber-100 text-slate-800 text-sm leading-relaxed">
                            {{ $changeRequest->client_notes }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right 1 Col: Metadata Card -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-4">
                <h3 class="text-xs font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100 pb-3">Project Metadata</h3>

                <div>
                    <span class="text-xs text-slate-400 block font-medium">Target Project</span>
                    @if($changeRequest->project)
                        <a href="{{ route('client.projects.show', $changeRequest->project->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors text-sm">
                            {{ $changeRequest->project->title }}
                        </a>
                    @else
                        <span class="text-sm font-bold text-slate-900">N/A</span>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4 pt-2">
                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Submitted Date</span>
                        <span class="text-xs font-bold text-slate-700 font-mono">{{ $changeRequest->created_at->format('M d, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Last Updated</span>
                        <span class="text-xs font-bold text-slate-700 font-mono">{{ $changeRequest->updated_at->format('M d, Y H:i') }}</span>
                    </div>
                </div>

                @if($changeRequest->reviewer)
                    <div class="pt-2 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block font-medium">Assigned Evaluator</span>
                        <span class="text-xs font-bold text-slate-800">{{ $changeRequest->reviewer->name }}</span>
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection

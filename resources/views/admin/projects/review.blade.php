@extends('layouts.admin')

@section('title', 'Project Review & Sign-Off - ' . $project->reference_number)

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200 pb-5">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.projects.show', $project->id) }}" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Project Review & Sign-Off</h1>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $project->reference_number }}
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1 ml-8">Client review lifecycle, structured feedback history, and final delivery workflow.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.projects.show', $project->id) }}" class="px-4 py-2 text-xs font-semibold bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-xl transition-colors">
                Back to Workspace
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Review Status Overview Card -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Current Review Status</span>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-lg font-bold text-slate-900">
                        {{ $signOff ? $signOff->status->label() : 'Not Yet Submitted for Review' }}
                    </span>
                    @if($signOff && $signOff->isApproved())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                            ✓ Client Approved
                        </span>
                    @elseif($signOff && $signOff->isPendingReview())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-100 text-amber-800 border border-amber-200">
                            Awaiting Client Input
                        </span>
                    @endif
                </div>
            </div>

            <div class="text-xs text-slate-500 space-y-1 sm:text-right">
                <div>Client: <strong class="text-slate-800">{{ $project->client?->name ?? 'N/A' }}</strong></div>
                <div>Project Status: <strong class="text-slate-800">{{ $project->status->label() }}</strong></div>
                @if($signOff && $signOff->accepted_at)
                    <div class="text-emerald-700 font-semibold">Accepted: {{ $signOff->accepted_at->format('M d, Y H:i') }}</div>
                @endif
            </div>
        </div>

        <!-- Request Review Action Form (for Admin) -->
        @if(! $signOff || ! $signOff->isApproved())
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200/80 space-y-3">
                <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Request / Re-request Client Review
                </h3>
                <p class="text-xs text-slate-600">This will update the project status to <strong>Client Review</strong> and send a review notification to the client.</p>
                
                <form action="{{ route('admin.projects.review.request', $project->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Review Instructions / Release Notes (Optional)</label>
                        <textarea name="review_notes" rows="2" class="w-full text-xs p-3 border border-slate-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="Specify deliverables ready for review, demo URL, testing credentials..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg transition-colors shadow-sm">
                            Send Review Request to Client
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Final Delivery Action Form (Only available after client approval) -->
        @if($signOff && $signOff->isApproved() && ! $signOff->final_delivery_at)
            <div class="bg-emerald-50 p-5 rounded-xl border border-emerald-200 space-y-3">
                <h3 class="text-sm font-bold text-emerald-900 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Record Final Project Delivery
                </h3>
                <p class="text-xs text-emerald-800">The client has granted final approval! Record final delivery to mark the project as <strong>Completed</strong>.</p>
                
                <form action="{{ route('admin.projects.review.deliver', $project->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-emerald-900 mb-1">Final Delivery Notes / Handover Details</label>
                        <textarea name="final_delivery_notes" rows="2" class="w-full text-xs p-3 border border-emerald-300 rounded-lg focus:outline-none focus:border-emerald-600" placeholder="Repository access, production deployment details, final documentation link..."></textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs rounded-lg transition-colors shadow-sm">
                            Confirm Final Delivery & Complete Project
                        </button>
                    </div>
                </form>
            </div>
        @endif

        @if($signOff && $signOff->final_delivery_at)
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1">
                <div class="font-bold text-slate-800">Final Delivery Completed</div>
                <div class="text-slate-600">Delivered on: {{ $signOff->final_delivery_at->format('M d, Y H:i A') }}</div>
                @if($signOff->final_delivery_notes)
                    <p class="text-slate-600 italic mt-1">"{{ $signOff->final_delivery_notes }}"</p>
                @endif
            </div>
        @endif
    </div>

    <!-- Review Iterations & History -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-200 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Review Iterations & Feedback History ({{ $iterations->count() }})
        </h3>

        <div class="space-y-4">
            @forelse($iterations as $iter)
                <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-xs text-slate-800">Iteration #{{ $iter->iteration_number }}</span>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-700">
                            {{ $iter->status->label() }}
                        </span>
                    </div>

                    @if($iter->review_notes)
                        <div class="text-xs bg-white p-3 rounded-lg border border-slate-200">
                            <span class="text-slate-400 font-semibold block uppercase text-[10px]">Staff Review Notes</span>
                            <p class="text-slate-700 mt-1 whitespace-pre-wrap">{{ $iter->review_notes }}</p>
                        </div>
                    @endif

                    @if($iter->client_feedback)
                        <div class="text-xs bg-amber-50/60 p-3 rounded-lg border border-amber-200">
                            <span class="text-amber-800 font-semibold block uppercase text-[10px]">Client Feedback / Revisions</span>
                            <p class="text-amber-900 mt-1 whitespace-pre-wrap">{{ $iter->client_feedback }}</p>
                            <span class="text-[10px] text-amber-700/80 block mt-1">Submitted on {{ $iter->submitted_at ? $iter->submitted_at->format('M d, Y H:i') : $iter->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8 text-slate-500 text-xs">
                    No review iterations recorded yet.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

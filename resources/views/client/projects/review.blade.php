@extends('layouts.client')

@section('title', 'Project Review & Sign-Off - ' . $project->reference_number)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-700/60 pb-5">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('client.projects.show', $project->id) }}" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-white tracking-tight">Project Review & Sign-Off</h1>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    {{ $project->reference_number }}
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1 ml-8">Review project deliverables, provide feedback, or grant final sign-off approval.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('client.projects.show', $project->id) }}" class="px-4 py-2 text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl transition-colors">
                Back to Workspace
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Review Status Overview Card -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 shadow-xl space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-700/60">
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Current Review Status</span>
                <div class="flex items-center gap-3 mt-1">
                    <span class="text-xl font-bold text-white">
                        {{ $signOff ? $signOff->status->label() : 'Not Yet Submitted for Review' }}
                    </span>
                    @if($signOff && $signOff->isApproved())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            ✓ Approved & Accepted
                        </span>
                    @elseif($signOff && $signOff->isPendingReview())
                        <span class="px-3 py-1 text-xs font-bold rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            Review Requested
                        </span>
                    @endif
                </div>
            </div>

            @if($signOff && $signOff->accepted_at)
                <div class="text-xs text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-4 py-2 rounded-xl">
                    <strong class="block font-semibold">Final Approval Recorded</strong>
                    <span>Accepted on {{ $signOff->accepted_at->format('M d, Y \a\t H:i') }}</span>
                </div>
            @endif
        </div>

        <!-- Latest Release / Review Notes from Staff -->
        @if($signOff && $signOff->currentIteration && $signOff->currentIteration->review_notes)
            <div class="bg-slate-900/60 border border-indigo-500/30 rounded-xl p-4 space-y-1">
                <span class="text-xs font-semibold text-indigo-400 uppercase tracking-wider block">Team Notes for Review</span>
                <p class="text-xs text-slate-200 leading-relaxed whitespace-pre-wrap">{{ $signOff->currentIteration->review_notes }}</p>
            </div>
        @endif

        <!-- Client Review Actions (Feedback or Approval) -->
        @if($signOff && ! $signOff->isApproved())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                <!-- Option 1: Provide Feedback / Request Revisions -->
                <div class="bg-slate-900/70 border border-slate-700/80 rounded-xl p-5 space-y-4">
                    <div class="flex items-center space-x-2 text-amber-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <h3 class="text-sm font-bold">Request Revisions / Feedback</h3>
                    </div>
                    <p class="text-xs text-slate-400">If adjustments or minor revisions are needed before final sign-off, submit your feedback below.</p>
                    
                    <form action="{{ route('client.projects.review.feedback', $project->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <textarea name="feedback" rows="4" required placeholder="Describe any layout tweaks, functional fixes, or feedback for the team..." 
                                class="w-full bg-slate-800 border border-slate-700 rounded-xl p-3 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 resize-none">{{ old('feedback') }}</textarea>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-amber-600 hover:bg-amber-500 text-white font-semibold text-xs rounded-xl transition-colors shadow-lg">
                            Submit Review Feedback
                        </button>
                    </form>
                </div>

                <!-- Option 2: Grant Final Sign-Off & Approval -->
                <div class="bg-slate-900/70 border border-emerald-500/30 rounded-xl p-5 space-y-4">
                    <div class="flex items-center space-x-2 text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-sm font-bold">Grant Final Approval & Acceptance</h3>
                    </div>
                    <p class="text-xs text-slate-400">If you have reviewed the delivered project work and are satisfied, submit your formal sign-off acceptance.</p>

                    <form action="{{ route('client.projects.review.approve', $project->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="p-3 bg-slate-800/80 rounded-xl border border-slate-700 text-xs text-slate-300">
                            <label class="flex items-start space-x-2 cursor-pointer">
                                <input type="checkbox" required class="mt-0.5 rounded bg-slate-900 border-slate-600 text-emerald-500 focus:ring-emerald-500">
                                <span class="leading-tight">I confirm that I have reviewed the delivered project deliverables and grant formal final approval for project <strong>{{ $project->reference_number }}</strong>.</span>
                            </label>
                        </div>

                        <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs rounded-xl transition-colors shadow-lg">
                            Submit Final Project Sign-Off
                        </button>
                    </form>
                </div>
            </div>
        @elseif(! $signOff)
            <div class="p-8 text-center bg-slate-900/40 rounded-xl border border-slate-700/60">
                <div class="w-12 h-12 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto text-slate-500 mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-semibold text-slate-300">Review Not Yet Open</h3>
                <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto">The Startiz Labs development team will request your review once project milestone deliverables are ready.</p>
            </div>
        @endif
    </div>

    <!-- Review Iterations & History -->
    @if($iterations->isNotEmpty())
        <div class="bg-slate-800/80 border border-slate-700/60 rounded-2xl p-6 shadow-xl space-y-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Feedback & Review History
            </h3>

            <div class="space-y-3">
                @foreach($iterations as $iter)
                    <div class="p-4 rounded-xl border border-slate-700/80 bg-slate-900/50 space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-300">Iteration #{{ $iter->iteration_number }}</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                                {{ $iter->status->label() }}
                            </span>
                        </div>

                        @if($iter->client_feedback)
                            <div class="bg-slate-800/80 p-3 rounded-lg border border-slate-700 text-slate-200">
                                <span class="text-emerald-400 font-semibold block text-[10px] uppercase">Your Feedback</span>
                                <p class="mt-1 whitespace-pre-wrap">{{ $iter->client_feedback }}</p>
                                <span class="text-[10px] text-slate-500 block mt-1">Submitted {{ $iter->submitted_at ? $iter->submitted_at->format('M d, Y H:i') : '' }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

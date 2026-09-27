@extends('layouts.client')

@section('title', $project->title . ' — Activity Timeline')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Back to Project Workspace -->
    <div>
        <a href="{{ route('client.projects.show', $project->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Workspace for {{ $project->title }}
        </a>
    </div>

    <!-- Header Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2">
                <span class="font-mono text-xs font-bold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-200">
                    {{ $project->reference_number }}
                </span>
                <span class="text-xs font-semibold text-slate-500">• {{ $project->service->name ?? 'Software Development' }}</span>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-2">
                Project Activity & Audit Timeline
            </h1>
            <p class="text-xs text-slate-500 mt-1">Verified activity, milestone progress, quotation, invoice, ticket, and document history.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('client.projects.show', $project->id) }}" class="px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                View Overview
            </a>
        </div>
    </div>

    <!-- Timeline List -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm p-6 sm:p-8">
        @if(count($activities) > 0)
            <div class="relative pl-6 space-y-8 before:absolute before:left-2.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-200">
                @foreach($activities as $activity)
                    <div class="relative flex items-start space-x-4">
                        <!-- Node Icon -->
                        <div class="absolute -left-6 mt-1 flex items-center justify-center w-5 h-5 rounded-full ring-4 ring-white bg-indigo-600 text-white">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        </div>

                        <!-- Event Content Box -->
                        <div class="flex-1 bg-slate-50/70 border border-slate-200/70 rounded-xl p-4 sm:p-5 shadow-2xs">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200/80 w-fit">
                                    {{ $activity->action_label }}
                                </span>
                                <time class="text-xs text-slate-400 font-medium">{{ $activity->created_at->format('M d, Y \a\t h:i A') }} ({{ $activity->created_at->diffForHumans() }})</time>
                            </div>

                            <p class="text-sm font-semibold text-slate-800 mt-2.5 leading-relaxed">{{ $activity->description }}</p>

                            @if($activity->actor)
                                <div class="text-xs text-slate-500 mt-2">
                                    Updated by <span class="font-bold text-slate-700">{{ $activity->actor->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($activities->hasPages())
                <div class="mt-8 pt-4 border-t border-slate-100">
                    {{ $activities->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="mt-2 text-sm font-bold text-slate-900">No project activity logged yet</h3>
                <p class="mt-1 text-xs text-slate-500">Activity and milestone updates will appear here as your project progresses.</p>
            </div>
        @endif
    </div>
</div>
@endsection

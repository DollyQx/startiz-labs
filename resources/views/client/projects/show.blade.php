@extends('layouts.client')

@section('title', $project->title . ' — Project Workspace')

@section('content')
<div class="space-y-8">

    <!-- Back Button & Breadcrumbs -->
    <div>
        <a href="{{ route('client.projects.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Projects Directory
        </a>
    </div>

    <!-- Project Header Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200">
                        {{ $project->reference_number }}
                    </span>
                    <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                        {{ is_object($project->status) && method_exists($project->status, 'label') ? $project->status->label() : Str::headline($project->status) }}
                    </span>
                    @if($project->priority)
                        <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                            {{ is_object($project->priority) && method_exists($project->priority, 'label') ? $project->priority->label() : Str::headline($project->priority) }} Priority
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    {{ $project->title }}
                </h1>
                
                <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m46 0v2m0 0a2 2 0 01-2 2H5a2 2 0 01-2-2V6"/></svg>
                        Service: <strong class="text-slate-800">{{ $project->service->name ?? 'Software Development' }}</strong>
                    </span>
                    <span>•</span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        Industry: <strong class="text-slate-800">{{ $project->industry->name ?? 'Technology' }}</strong>
                    </span>
                </div>
            </div>

            <!-- Schedule Dates & Activity Timeline Card -->
            <div class="bg-slate-50 p-4 rounded-xl border border-slate-200/80 text-xs space-y-2 min-w-[240px]">
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold uppercase tracking-wider">Start Date:</span>
                    <span class="font-bold text-slate-900">{{ $project->start_date ? $project->start_date->format('M d, Y') : 'TBD' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500 font-semibold uppercase tracking-wider">Est. Completion:</span>
                    <span class="font-bold text-slate-900">{{ $project->expected_completion_date ? $project->expected_completion_date->format('M d, Y') : 'TBD' }}</span>
                </div>
                @if($project->actual_completion_date)
                    <div class="flex justify-between border-t border-slate-200/60 pt-1.5 text-emerald-700">
                        <span class="font-semibold uppercase tracking-wider">Completed On:</span>
                        <span class="font-bold">{{ $project->actual_completion_date->format('M d, Y') }}</span>
                    </div>
                @endif
                <div class="border-t border-slate-200/60 pt-2 space-y-2">
                    <a href="{{ route('client.projects.review', $project->id) }}" class="inline-flex items-center justify-center gap-1.5 w-full px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-sm transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Review & Sign-Off
                    </a>
                    <a href="{{ route('client.projects.messages', $project->id) }}" class="inline-flex items-center justify-center gap-1.5 w-full px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        Project Messages
                    </a>
                    <a href="{{ route('client.projects.activity', $project->id) }}" class="inline-flex items-center justify-center gap-1.5 w-full px-3 py-1.5 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold border border-indigo-200 text-xs transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        View Activity Timeline
                    </a>
                </div>
            </div>
        </div>

        <!-- Progress Overview Bar -->
        <div class="space-y-2">
            <div class="flex items-center justify-between text-xs font-bold">
                <span class="text-slate-600 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Milestone Completion Progress
                </span>
                <span class="font-mono text-sm text-blue-700">{{ $progressPercent }}%</span>
            </div>
            <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden border border-slate-200/70">
                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all duration-500" style="width: {{ $progressPercent }}%"></div>
            </div>
        </div>

        <!-- Project Description -->
        @if($project->description)
            <div class="pt-2">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Project Overview & Description</h3>
                <div class="text-sm text-slate-700 bg-slate-50/70 p-4 rounded-xl border border-slate-200/60 leading-relaxed whitespace-pre-line">
                    {{ $project->description }}
                </div>
            </div>
        @endif
    </div>

    <!-- Milestone Roadmap -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Milestone Roadmap
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Sequential development phases and delivery milestones.</p>
        </div>

        <div class="space-y-4">
            @forelse($project->milestones as $ms)
                <div class="p-5 rounded-xl border border-slate-200/80 bg-slate-50/50 hover:bg-white transition-all space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-700 font-extrabold text-xs flex items-center justify-center border border-indigo-200">
                                {{ $ms->sequence_order ?? $loop->iteration }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-sm">
                                {{ $ms->title }}
                            </h3>
                        </div>
                        <div class="flex items-center gap-2 text-xs">
                            <span class="inline-flex px-2.5 py-0.5 font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                {{ is_object($ms->status) && method_exists($ms->status, 'label') ? $ms->status->label() : Str::headline($ms->status) }}
                            </span>
                            @if($ms->due_date)
                                <span class="text-slate-500">Due: <strong class="text-slate-800">{{ $ms->due_date->format('M d, Y') }}</strong></span>
                            @endif
                        </div>
                    </div>

                    @if($ms->description)
                        <p class="text-xs text-slate-600 pl-10">
                            {{ $ms->description }}
                        </p>
                    @endif
                </div>
            @empty
                <div class="p-6 text-center text-xs text-slate-500 bg-slate-50 rounded-xl border border-slate-200/60">
                    No milestones configured for this project yet.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Project Requirements & Deliverable Tasks Grid (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Requirements -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5 flex flex-col">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Project Requirements
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Approved technical and functional specifications.</p>
            </div>

            <div class="space-y-3 flex-1">
                @forelse($project->requirements as $req)
                    <div class="p-4 rounded-xl border border-slate-200/70 bg-slate-50/50 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <h4 class="font-semibold text-slate-900 text-xs">{{ $req->title }}</h4>
                            <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                {{ is_object($req->status) && method_exists($req->status, 'label') ? $req->status->label() : Str::headline($req->status) }}
                            </span>
                        </div>
                        @if($req->description)
                            <p class="text-[11px] text-slate-500 leading-relaxed">{{ Str::limit($req->description, 120) }}</p>
                        @endif
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500 bg-slate-50 rounded-xl border border-slate-200/60">
                        No requirements linked yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Deliverable Tasks -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-5 flex flex-col">
            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Deliverable Tasks
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Client-facing task progress and status.</p>
            </div>

            <div class="space-y-3 flex-1">
                @forelse($project->tasks as $task)
                    <div class="p-4 rounded-xl border border-slate-200/70 bg-slate-50/50 flex items-center justify-between gap-3">
                        <div>
                            <h4 class="font-semibold text-slate-900 text-xs">{{ $task->title }}</h4>
                            @if($task->due_date)
                                <p class="text-[11px] text-slate-500 mt-0.5">Due: {{ $task->due_date->format('M d, Y') }}</p>
                            @endif
                        </div>
                        <span class="inline-flex px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                            {{ is_object($task->status) && method_exists($task->status, 'label') ? $task->status->label() : Str::headline($task->status) }}
                        </span>
                    </div>
                @empty
                    <div class="p-6 text-center text-xs text-slate-500 bg-slate-50 rounded-xl border border-slate-200/60">
                        No tasks published yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

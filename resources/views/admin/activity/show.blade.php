@extends('layouts.admin')

@section('title', 'Activity Audit Record #' . $activity->id)

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Header with Back Button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.activity.index') }}" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Audit Log Record #{{ $activity->id }}</h1>
                <p class="text-sm text-slate-400 mt-0.5">Recorded on {{ $activity->created_at->format('F d, Y \a\t h:i:s A') }} ({{ $activity->created_at->diffForHumans() }})</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
            {{ $activity->action_label }}
        </span>
    </div>

    <!-- Details Card Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Event Overview -->
        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-6 backdrop-blur-sm space-y-4">
            <h2 class="text-lg font-semibold text-white border-b border-slate-700/60 pb-3">Event Overview</h2>
            
            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-1 border-b border-slate-700/30">
                    <span class="text-slate-400 font-medium">Action Name</span>
                    <span class="text-white font-mono text-xs bg-slate-900 px-2 py-0.5 rounded border border-slate-700/50">{{ $activity->action }}</span>
                </div>

                <div class="flex justify-between py-1 border-b border-slate-700/30">
                    <span class="text-slate-400 font-medium">Actor / User</span>
                    <span class="text-slate-200">
                        @if($activity->actor)
                            {{ $activity->actor->name }} ({{ ucfirst(str_replace('_', ' ', $activity->actor->role)) }})
                        @else
                            <span class="text-slate-500 italic">System / Automated Event</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between py-1 border-b border-slate-700/30">
                    <span class="text-slate-400 font-medium">Associated Project</span>
                    <span class="text-slate-200">
                        @if($activity->project)
                            <a href="{{ route('admin.projects.show', $activity->project->id) }}" class="text-indigo-400 hover:underline">
                                {{ $activity->project->title }}
                            </a>
                        @else
                            <span class="text-slate-500">None</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between py-1 border-b border-slate-700/30">
                    <span class="text-slate-400 font-medium">Associated Client</span>
                    <span class="text-slate-200">
                        @if($activity->client)
                            {{ $activity->client->name }} ({{ $activity->client->email }})
                        @else
                            <span class="text-slate-500">None</span>
                        @endif
                    </span>
                </div>

                <div class="flex justify-between py-1 border-b border-slate-700/30">
                    <span class="text-slate-400 font-medium">Subject Class</span>
                    <span class="text-slate-300 font-mono text-xs">
                        {{ $activity->subject_type ? class_basename($activity->subject_type) . ' #' . $activity->subject_id : 'None' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Request Context -->
        <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-6 backdrop-blur-sm space-y-4">
            <h2 class="text-lg font-semibold text-white border-b border-slate-700/60 pb-3">Security & Request Context</h2>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between py-1 border-b border-slate-700/30">
                    <span class="text-slate-400 font-medium">IP Address</span>
                    <span class="text-slate-200 font-mono text-xs">{{ $activity->ip_address ?: 'Not recorded' }}</span>
                </div>

                <div class="py-1 border-b border-slate-700/30">
                    <span class="text-slate-400 font-medium block mb-1">User Agent</span>
                    <span class="text-slate-300 text-xs font-mono break-all bg-slate-900/80 p-2 rounded block border border-slate-700/40">
                        {{ $activity->user_agent ?: 'Not recorded' }}
                    </span>
                </div>

                <div class="py-1">
                    <span class="text-slate-400 font-medium block mb-1">Description</span>
                    <p class="text-slate-200 text-sm bg-slate-900/60 p-3 rounded-lg border border-slate-700/40">
                        {{ $activity->description ?: 'No description logged for this event.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Sanitized Metadata Box -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-6 backdrop-blur-sm space-y-4">
        <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
            <h2 class="text-lg font-semibold text-white">Event Payload Metadata (Sanitized)</h2>
            <span class="text-xs text-slate-400">Sensitive keys auto-redacted</span>
        </div>

        @if($metadata && count($metadata) > 0)
            <pre class="bg-slate-900/90 text-emerald-400 p-4 rounded-xl text-xs font-mono overflow-x-auto border border-slate-700/60">{{ json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
        @else
            <p class="text-sm text-slate-400 italic">No additional metadata payload attached to this log entry.</p>
        @endif
    </div>
</div>
@endsection

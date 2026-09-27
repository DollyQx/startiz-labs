@extends('layouts.admin')

@section('title', 'System Activity & Audit Logs')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white tracking-tight">System Audit & Activity Logs</h1>
            <p class="text-sm text-slate-400 mt-1">Track business operations, user actions, system modifications, and security events.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                Total Logs: {{ $activities->total() }}
            </span>
        </div>
    </div>

    <!-- Multi-Filter Toolbar -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl p-5 backdrop-blur-sm">
        <form method="GET" action="{{ route('admin.activity.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search input -->
                <div>
                    <label for="search" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Search Description / IP</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="e.g. project title, IP address..." 
                        class="w-full bg-slate-900/90 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Action Dropdown -->
                <div>
                    <label for="action" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Action</label>
                    <select name="action" id="action" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                        <option value="">All Actions</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>
                                {{ \App\Services\ActivityVisibilityService::getActionLabel($act) }} ({{ $act }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Entity Type Dropdown -->
                <div>
                    <label for="entity_type" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Entity Type</label>
                    <select name="entity_type" id="entity_type" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                        <option value="">All Entity Types</option>
                        @foreach($entityTypes as $key => $label)
                            <option value="{{ $key }}" {{ request('entity_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Client Filter -->
                <div>
                    <label for="client_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Client Account</label>
                    <select name="client_id" id="client_id" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                        <option value="">All Clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (string) request('client_id') === (string) $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2 border-t border-slate-700/40">
                <!-- Project Filter -->
                <div>
                    <label for="project_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Project</label>
                    <select name="project_id" id="project_id" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                        <option value="">All Projects</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ (string) request('project_id') === (string) $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Actor Filter -->
                <div>
                    <label for="actor_id" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Actor (User)</label>
                    <select name="actor_id" id="actor_id" class="w-full bg-slate-900/90 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                        <option value="">All Users</option>
                        @foreach($actors as $actUser)
                            <option value="{{ $actUser->id }}" {{ (string) request('actor_id') === (string) $actUser->id ? 'selected' : '' }}>{{ $actUser->name }} ({{ ucfirst(is_object($actUser->role) ? $actUser->role->value : $actUser->role) }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                        class="w-full bg-slate-900/90 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Date To & Submit Button -->
                <div class="flex items-end space-x-2">
                    <div class="flex-1">
                        <label for="date_to" class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1.5">To Date</label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                            class="w-full bg-slate-900/90 border border-slate-700/80 rounded-lg px-3.5 py-2 text-sm text-white focus:outline-none focus:border-indigo-500">
                    </div>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white font-medium text-sm px-4 py-2 rounded-lg transition-colors duration-150">
                        Filter
                    </button>
                    @if(request()->filled('search') || request()->filled('action') || request()->filled('entity_type') || request()->filled('client_id') || request()->filled('project_id') || request()->filled('actor_id') || request()->filled('date_from') || request()->filled('date_to'))
                        <a href="{{ route('admin.activity.index') }}" class="bg-slate-700 hover:bg-slate-600 text-slate-300 font-medium text-sm px-3 py-2 rounded-lg transition-colors">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Activity Log Table -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/80 text-xs uppercase font-semibold text-slate-400 border-b border-slate-700/60">
                    <tr>
                        <th class="px-6 py-4">Timestamp</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Actor</th>
                        <th class="px-6 py-4">Subject / Entity</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4 text-right">Details</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-slate-700/20 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium text-white">{{ $activity->created_at->format('M d, Y H:i') }}</div>
                                <div class="text-xs text-slate-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold 
                                    @if(str_contains($activity->action, 'created') || str_contains($activity->action, 'approved')) bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                    @elseif(str_contains($activity->action, 'rejected') || str_contains($activity->action, 'deleted')) bg-rose-500/10 text-rose-400 border border-rose-500/20
                                    @elseif(str_contains($activity->action, 'internal')) bg-amber-500/10 text-amber-400 border border-amber-500/20
                                    @else bg-sky-500/10 text-sky-400 border border-sky-500/20 @endif">
                                    {{ $activity->action_label }}
                                </span>
                                <div class="text-[11px] text-slate-500 font-mono mt-0.5">{{ $activity->action }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->actor)
                                    <div class="font-medium text-slate-200">{{ $activity->actor->name }}</div>
                                    <div class="text-xs text-slate-400">{{ ucfirst(str_replace('_', ' ', is_object($activity->actor->role) ? $activity->actor->role->value : $activity->actor->role)) }}</div>
                                @else
                                    <span class="text-slate-500 italic">System / Guest</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->project)
                                    <div class="text-xs font-medium text-indigo-400">{{ $activity->project->title }}</div>
                                @endif
                                @if($activity->client)
                                    <div class="text-xs text-slate-400">Client: {{ $activity->client->name }}</div>
                                @endif
                                @if(!$activity->project && !$activity->client && $activity->subject_type)
                                    <div class="text-xs text-slate-400 font-mono">{{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-300 line-clamp-2 max-w-md">{{ $activity->description ?: 'No description provided' }}</p>
                            </td>
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('admin.activity.show', $activity->id) }}" 
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-slate-700/80 hover:bg-slate-600 text-slate-200 border border-slate-600/50 transition-colors">
                                    View Audit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-slate-400 font-medium">No activity records match your search criteria.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activities->hasPages())
            <div class="px-6 py-4 border-t border-slate-700/60 bg-slate-900/40">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

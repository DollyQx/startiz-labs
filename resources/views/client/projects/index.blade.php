@extends('layouts.client')

@section('title', 'My Projects')

@section('content')
<div class="space-y-6">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">My Projects</h1>
            <p class="text-sm text-slate-500 mt-1">Track software development progress, milestone roadmaps, and requirements.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full border border-blue-200">
                {{ $projects->total() }} {{ Str::plural('Project', $projects->total()) }} Total
            </span>
        </div>
    </div>

    <!-- Search & Status Filter Bar -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('client.projects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            
            <!-- Search Query -->
            <div>
                <label for="search" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Search</label>
                <div class="relative">
                    <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Project title, ref..." class="w-full pl-9 pr-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Status</label>
                <select id="status" name="status" class="w-full px-3 py-2 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 bg-white">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="sm:col-span-2 lg:col-span-2 flex items-end gap-2 pt-2 sm:pt-0">
                <button type="submit" class="bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm px-5 py-2 rounded-xl transition-all shadow-sm">
                    Filter Projects
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('client.projects.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm px-4 py-2 rounded-xl transition-all">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Projects Grid / Directory List -->
    <div class="space-y-4">
        @forelse($projects as $proj)
            @php
                $totalM = $proj->milestones->count();
                $compM = $proj->milestones->filter(fn($m) => $m->status === \App\Enums\MilestoneStatus::COMPLETED || (is_string($m->status) && $m->status === 'completed'))->count();
                $pPercent = $totalM > 0 ? (int)round(($compM / $totalM) * 100) : ($proj->status === \App\Enums\ProjectStatus::COMPLETED ? 100 : 0);
            @endphp
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:border-blue-300 transition-all space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-0.5 rounded-md border border-blue-200">
                                {{ $proj->reference_number }}
                            </span>
                            <span class="inline-flex px-2.5 py-0.5 text-xs font-bold rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                                {{ is_object($proj->status) && method_exists($proj->status, 'label') ? $proj->status->label() : Str::headline($proj->status) }}
                            </span>
                            @if($proj->priority)
                                <span class="inline-flex px-2 py-0.5 text-[11px] font-bold rounded-full bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ is_object($proj->priority) && method_exists($proj->priority, 'label') ? $proj->priority->label() : Str::headline($proj->priority) }}
                                </span>
                            @endif
                        </div>
                        <h2 class="text-lg font-bold text-slate-900 tracking-tight">
                            <a href="{{ route('client.projects.show', $proj->id) }}" class="hover:text-blue-600 transition-colors">
                                {{ $proj->title }}
                            </a>
                        </h2>
                    </div>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('client.projects.show', $proj->id) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs px-4 py-2 rounded-xl transition-all shadow-sm">
                            <span>Open Workspace</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                    <!-- Service & Industry -->
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase tracking-wider mb-1">Service & Industry</span>
                        <div class="font-bold text-slate-800">{{ $proj->service->name ?? 'Custom Software' }}</div>
                        <div class="text-slate-500 mt-0.5">{{ $proj->industry->name ?? 'Enterprise Services' }}</div>
                    </div>

                    <!-- Timeline -->
                    <div>
                        <span class="text-slate-400 font-semibold block uppercase tracking-wider mb-1">Timeline Schedule</span>
                        <div class="text-slate-700">Start: <span class="font-bold text-slate-900">{{ $proj->start_date ? $proj->start_date->format('M d, Y') : 'TBD' }}</span></div>
                        <div class="text-slate-700 mt-0.5">Est. Completion: <span class="font-bold text-slate-900">{{ $proj->expected_completion_date ? $proj->expected_completion_date->format('M d, Y') : 'TBD' }}</span></div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                            <span class="text-slate-500 uppercase tracking-wider">Roadmap Progress</span>
                            <span class="text-blue-600 font-mono">{{ $pPercent }}%</span>
                        </div>
                        <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden border border-slate-200/60">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all duration-500" style="width: {{ $pPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-12 rounded-2xl border border-slate-200/80 shadow-sm text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">No projects found</h3>
                    <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                        @if(request()->hasAny(['search', 'status']))
                            No active projects match your search filters. Try adjusting or resetting your search criteria.
                        @else
                            Your active software development projects will appear here once your account project is created.
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($projects->hasPages())
        <div class="pt-2">
            {{ $projects->links() }}
        </div>
    @endif

</div>
@endsection

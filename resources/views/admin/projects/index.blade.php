@extends('layouts.admin')

@section('title', 'Project Directory')
@section('breadcrumb', 'Projects')

@section('content')
<div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Project Directory</h1>
            <p class="text-sm text-slate-500 mt-1">Manage active project delivery, milestones, requirements and tasks.</p>
        </div>
        <div>
            <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create New Project
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.projects.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Query -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-xs font-semibold text-slate-700 mb-1">Search</label>
                <input type="text" name="search" id="search" value="{{ $search }}" placeholder="Ref number, title, client name..." class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                <select name="status" id="status" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label for="priority" class="block text-xs font-semibold text-slate-700 mb-1">Priority</label>
                <select name="priority" id="priority" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Priorities</option>
                    @foreach($priorities as $pr)
                        <option value="{{ $pr->value }}" {{ $priority === $pr->value ? 'selected' : '' }}>
                            {{ $pr->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Client Filter & Filter Action Buttons -->
            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label for="client_id" class="block text-xs font-semibold text-slate-700 mb-1">Client</label>
                    <select name="client_id" id="client_id" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Clients</option>
                        @foreach($clients as $c)
                            <option value="{{ $c->id }}" {{ (string)$clientId === (string)$c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-3 py-2 bg-slate-900 text-white rounded-lg text-xs font-bold hover:bg-slate-800 transition-colors">
                    Filter
                </button>
                @if($search || $status || $priority || $clientId)
                    <a href="{{ route('admin.projects.index') }}" class="px-3 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-semibold hover:bg-slate-200 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Projects Table Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase tracking-wider">
                        <th class="px-4 py-3">Reference</th>
                        <th class="px-4 py-3">Project Title</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Service & Industry</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Priority</th>
                        <th class="px-4 py-3">Timeline</th>
                        <th class="px-4 py-3">Estimated Value</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-xs">
                    @forelse($projects as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-4 py-3 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.projects.show', $p->id) }}" class="text-blue-600 hover:underline">
                                    {{ $p->reference_number }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.projects.show', $p->id) }}" class="font-bold text-slate-900 hover:text-blue-600 block">
                                    {{ $p->title }}
                                </a>
                            </td>
                            <td class="px-4 py-3">
                                @if($p->client)
                                    <a href="{{ route('admin.clients.show', $p->client->id) }}" class="font-semibold text-slate-900 hover:text-blue-600">
                                        {{ $p->client->name }}
                                    </a>
                                @else
                                    <span class="text-slate-400">Unassigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">
                                <div>{{ $p->service->name ?? 'General Service' }}</div>
                                <div class="text-[10px] text-slate-400">{{ $p->industry->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full font-bold text-[11px] border
                                    {{ $p->status->value === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                    {{ $p->status->value === 'cancelled' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                    {{ $p->status->value === 'on_hold' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ in_array($p->status->value, ['planning', 'approved', 'in_progress', 'testing', 'client_review', 'deployment']) ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                ">
                                    {{ $p->status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider border
                                    {{ $p->priority->value === 'urgent' ? 'bg-rose-100 text-rose-800 border-rose-200' : '' }}
                                    {{ $p->priority->value === 'high' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                                    {{ $p->priority->value === 'medium' ? 'bg-blue-100 text-blue-800 border-blue-200' : '' }}
                                    {{ $p->priority->value === 'low' ? 'bg-slate-100 text-slate-700 border-slate-200' : '' }}
                                ">
                                    {{ $p->priority->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                <div>Start: {{ $p->start_date ? $p->start_date->format('M d, Y') : 'N/A' }}</div>
                                <div class="text-[10px] text-slate-400">Due: {{ $p->expected_completion_date ? $p->expected_completion_date->format('M d, Y') : 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3 font-semibold text-slate-900">
                                ₹{{ number_format($p->estimated_value, 2) }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.projects.show', $p->id) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold rounded-lg text-xs inline-flex items-center gap-1 transition-colors">
                                    Workspace &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-12 text-center text-slate-500">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                <p class="text-base font-bold text-slate-800">No Projects Found</p>
                                <p class="text-xs text-slate-500 mt-1">No delivery projects matched your current search and filter criteria.</p>
                                <div class="mt-4">
                                    <a href="{{ route('admin.projects.create') }}" class="btn-base btn-primary btn-sm">
                                        Create First Project
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($projects->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $projects->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

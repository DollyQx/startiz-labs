@extends('layouts.client')

@section('title', 'Change Requests')

@section('content')
<div class="space-y-6">

    <!-- Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Project Change Requests</h1>
            <p class="text-sm text-slate-500 mt-1">Submit scope adjustment or feature modification requests for your ongoing projects.</p>
        </div>
        <div>
            <a href="{{ route('client.change-requests.create') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Change Request
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('client.change-requests.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div class="sm:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title or reference number..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div>
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                    <option value="">All Statuses</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ request('status') === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <select name="priority" onchange="this.form.submit()" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                    <option value="">All Priorities</option>
                    @foreach($priorities as $pr)
                        <option value="{{ $pr->value }}" {{ request('priority') === $pr->value ? 'selected' : '' }}>
                            {{ $pr->label() }}
                        </option>
                    @endforeach
                </select>

                @if(request('search') || request('status') || request('priority'))
                    <a href="{{ route('client.change-requests.index') }}" class="px-3 py-2.5 rounded-xl border border-slate-200 text-slate-500 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 text-xs font-semibold flex items-center gap-1 transition-all">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-6">Reference</th>
                        <th class="py-3.5 px-6">Project & Title</th>
                        <th class="py-3.5 px-6 text-center">Priority</th>
                        <th class="py-3.5 px-6 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Est. Cost</th>
                        <th class="py-3.5 px-6">Submitted</th>
                        <th class="py-3.5 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($changeRequests as $cr)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-blue-600">
                                {{ $cr->reference_number }}
                            </td>
                            <td class="py-4 px-6">
                                <a href="{{ route('client.change-requests.show', $cr->id) }}" class="font-bold text-slate-900 hover:text-blue-600 transition-colors block">
                                    {{ $cr->title }}
                                </a>
                                @if($cr->project)
                                    <span class="text-xs text-slate-400 block font-mono">
                                        {{ $cr->project->title }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $cr->priority->badgeClass() }}">
                                    {{ $cr->priority->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $cr->status->badgeClass() }}">
                                    {{ $cr->status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right font-mono text-xs font-bold text-slate-800">
                                {{ $cr->estimated_cost !== null ? '₹' . number_format($cr->estimated_cost, 2) : 'TBD' }}
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $cr->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('client.change-requests.show', $cr->id) }}" class="inline-flex items-center gap-1 text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline">
                                    <span>Details</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                No change requests found. Click "New Change Request" to submit a project scope update.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($changeRequests->hasPages())
            <div class="px-6 py-4 border-t border-slate-200/80 bg-slate-50/50">
                {{ $changeRequests->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

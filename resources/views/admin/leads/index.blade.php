@extends('layouts.admin')

@section('title', 'Leads Pipeline')
@section('breadcrumb', 'Leads')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Leads Pipeline</h1>
            <p class="text-sm text-slate-500 mt-0.5">Track, assign, and qualify business requirement inquiries.</p>
        </div>
        <div>
            <a href="{{ route('admin.leads.create') }}" class="btn-base btn-primary btn-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Create New Lead
            </a>
        </div>
    </div>

    <!-- Search & Pipeline Filter Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.leads.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search reference, name, email, or company..." class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="w-full sm:w-56">
                <select name="status" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">All Pipeline Stages</option>
                    @foreach($statuses as $st)
                        <option value="{{ $st->value }}" {{ $status === $st->value ? 'selected' : '' }}>
                            {{ $st->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="btn-base btn-primary btn-sm w-full sm:w-auto">
                    Filter
                </button>
                @if(!empty($search) || !empty($status))
                    <a href="{{ route('admin.leads.index') }}" class="btn-base btn-outline btn-sm w-full sm:w-auto text-slate-600">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Leads Data Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Reference</th>
                        <th class="px-6 py-3.5">Contact Name & Email</th>
                        <th class="px-6 py-3.5">Company</th>
                        <th class="px-6 py-3.5">Stage</th>
                        <th class="px-6 py-3.5">Assigned To</th>
                        <th class="px-6 py-3.5">Created Date</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($leads as $lead)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 font-mono text-xs font-bold text-blue-600">
                                {{ $lead->reference_number }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $lead->name }}</div>
                                <div class="text-xs text-slate-500">{{ $lead->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-800">
                                {{ $lead->company_name ?? 'Individual' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-bold rounded-full border 
                                    {{ $lead->status->value === 'won' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                                    {{ $lead->status->value === 'lost' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                    {{ $lead->status->value === 'new' ? 'bg-slate-100 text-slate-800 border-slate-200' : '' }}
                                    {{ in_array($lead->status->value, ['contacted', 'qualified', 'quotation_sent', 'negotiation']) ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                ">
                                    {{ $lead->status->label() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $lead->assignedUser->name ?? 'Unassigned' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $lead->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-2.5 py-1.5 rounded border border-blue-100">
                                        View
                                    </a>
                                    <a href="{{ route('admin.leads.edit', $lead->id) }}" class="text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 px-2.5 py-1.5 rounded border border-slate-200">
                                        Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500 text-sm">
                                No leads found matching the filter criteria. <a href="{{ route('admin.leads.create') }}" class="text-blue-600 font-semibold hover:underline">Create a lead inquiry</a>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($leads->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

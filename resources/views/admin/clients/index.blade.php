@extends('layouts.admin')

@section('title', 'Clients Directory')
@section('breadcrumb', 'Clients')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Clients Directory</h1>
            <p class="text-sm text-slate-500 mt-0.5">Manage registered client organizations and account status.</p>
        </div>
    </div>

    <!-- Search & Filter Controls Bar -->
    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.clients.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-4">
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search client name, email, or company..." class="w-full pl-9 pr-4 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
            </div>

            <div class="w-full sm:w-48">
                <select name="status" class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 bg-white text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">All Account Statuses</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ $status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="btn-base btn-primary btn-sm w-full sm:w-auto">
                    Filter
                </button>
                @if(!empty($search) || !empty($status))
                    <a href="{{ route('admin.clients.index') }}" class="btn-base btn-outline btn-sm w-full sm:w-auto text-slate-600">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Clients Data Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3.5">Client Name</th>
                        <th class="px-6 py-3.5">Company</th>
                        <th class="px-6 py-3.5">Contact Phone</th>
                        <th class="px-6 py-3.5">Account Status</th>
                        <th class="px-6 py-3.5">Joined Date</th>
                        <th class="px-6 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($clients as $client)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $client->name }}</div>
                                <div class="text-xs text-slate-500">{{ $client->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs font-medium text-slate-800">
                                {{ $client->clientProfile->company_name ?? 'Individual' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-600">
                                {{ $client->phone ?? $client->clientProfile->phone ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-0.5 text-xs font-bold rounded-full {{ $client->status->value === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : ($client->status->value === 'suspended' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-amber-50 text-amber-700 border border-amber-200') }}">
                                    {{ ucfirst($client->status->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-slate-500">
                                {{ $client->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.clients.show', $client->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-100 hover:bg-blue-100 transition-colors">
                                    View Profile
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-500 text-sm">
                                No clients found matching the specified search or filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($clients->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $clients->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

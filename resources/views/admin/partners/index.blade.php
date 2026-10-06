@extends('layouts.admin')

@section('title', 'Partners Directory')
@section('breadcrumb', 'Partner Management')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Partner Program Directory</h1>
            <p class="text-xs text-slate-500 mt-1">Manage affiliate partners, approve accounts, customize commission rates, and audit referrals</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.partners.commissions') }}" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-bold transition-colors">
                Commissions &rarr;
            </a>
            <a href="{{ route('admin.partners.payouts') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors">
                Disburse Payouts &rarr;
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Partners</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Pending Approval</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pending']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Active & Approved</span>
            <div class="text-2xl font-black text-emerald-600 mt-1">{{ number_format($stats['approved']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Suspended / Rejected</span>
            <div class="text-2xl font-black text-rose-600 mt-1">{{ number_format($stats['suspended']) }}</div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.partners.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ empty($statusFilter) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                All
            </a>
            <a href="{{ route('admin.partners.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Pending ({{ $stats['pending'] }})
            </a>
            <a href="{{ route('admin.partners.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'approved' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Approved ({{ $stats['approved'] }})
            </a>
            <a href="{{ route('admin.partners.index', ['status' => 'suspended']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'suspended' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Suspended ({{ $stats['suspended'] }})
            </a>
        </div>

        <!-- Search Bar -->
        <form method="GET" action="{{ route('admin.partners.index') }}" class="flex items-center gap-2">
            @if($statusFilter)
                <input type="hidden" name="status" value="{{ $statusFilter }}">
            @endif
            <input type="text" name="search" value="{{ $search }}" placeholder="Search code, name, email..." class="bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold">Search</button>
            @if($search)
                <a href="{{ route('admin.partners.index', ['status' => $statusFilter]) }}" class="text-xs text-slate-400 hover:text-slate-600">Clear</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Referral Code</th>
                        <th class="py-3 px-4">Partner Details</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Commission Rate</th>
                        <th class="py-3 px-4">Payout Method</th>
                        <th class="py-3 px-4">Applied</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($partners as $partner)
                        <tr class="hover:bg-slate-50/75">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">
                                <a href="{{ route('admin.partners.show', $partner) }}" class="text-blue-600 hover:underline">
                                    {{ $partner->referral_code }}
                                </a>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-900">{{ $partner->user?->name ?? '—' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $partner->user?->email }}</div>
                                @if($partner->company_name)
                                    <div class="text-[11px] text-slate-400">{{ $partner->company_name }}</div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase
                                    {{ $partner->status->value === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $partner->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $partner->status->value === 'suspended' ? 'bg-rose-100 text-rose-800' : '' }}
                                    {{ $partner->status->value === 'rejected' ? 'bg-slate-100 text-slate-800' : '' }}
                                ">
                                    {{ $partner->status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <form method="POST" action="{{ route('admin.partners.rate', $partner) }}" class="flex items-center gap-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="number" step="0.01" min="0" max="100" name="commission_rate" value="{{ $partner->commission_rate }}" class="w-16 bg-slate-50 border border-slate-200 rounded px-1.5 py-1 text-xs font-bold text-slate-800 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span class="text-slate-400">%</span>
                                    <button type="submit" class="p-1 text-blue-600 hover:text-blue-800" title="Save Rate">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                </form>
                            </td>
                            <td class="py-3 px-4 font-mono text-[11px] uppercase">
                                {{ $partner->payout_method }}
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                {{ $partner->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($partner->status->value === 'pending')
                                        <form method="POST" action="{{ route('admin.partners.status', $partner) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.partners.status', $partner) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="rejected">
                                            <button type="submit" class="px-2 py-1 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded text-[10px] font-bold">Reject</button>
                                        </form>
                                    @elseif($partner->status->value === 'approved')
                                        <form method="POST" action="{{ route('admin.partners.status', $partner) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="submit" class="px-2 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded text-[10px] font-bold">Suspend</button>
                                        </form>
                                    @elseif($partner->status->value === 'suspended')
                                        <form method="POST" action="{{ route('admin.partners.status', $partner) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="approved">
                                            <button type="submit" class="px-2 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold">Reactivate</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.partners.show', $partner) }}" class="px-2 py-1 text-slate-600 hover:text-slate-900 text-[10px] font-bold">View &rarr;</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 px-4 text-center text-slate-400">No partner records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($partners->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $partners->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

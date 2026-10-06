@extends('layouts.admin')

@section('title', 'Partner Commissions Audit & Approval')
@section('breadcrumb', 'Partner Commissions')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Partner Commissions</h1>
            <p class="text-xs text-slate-500 mt-1">Review, approve, reject, and prepare verified partner commissions for payout</p>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="document.getElementById('new-commission-modal').classList.toggle('hidden')" class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition-colors">
                + Create Commission
            </button>
            <a href="{{ route('admin.partners.payouts') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors">
                Disburse Payouts &rarr;
            </a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Commissions</span>
            <div class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Pending Review</span>
            <div class="text-2xl font-black text-amber-600 mt-1">{{ number_format($stats['pending']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600">Approved & Payable</span>
            <div class="text-2xl font-black text-blue-600 mt-1">{{ number_format($stats['approved']) }}</div>
        </div>
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Total Settled Amount</span>
            <div class="text-2xl font-black text-emerald-600 mt-1 font-mono">₹{{ number_format($stats['total_paid_amount'], 2) }}</div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-xl border border-slate-200 p-3 shadow-sm flex items-center gap-2">
        <a href="{{ route('admin.partners.commissions') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ empty($statusFilter) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">All</a>
        <a href="{{ route('admin.partners.commissions', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Pending Review ({{ $stats['pending'] }})</a>
        <a href="{{ route('admin.partners.commissions', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'approved' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Approved</a>
        <a href="{{ route('admin.partners.commissions', ['status' => 'payable']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'payable' ? 'bg-purple-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Payable</a>
        <a href="{{ route('admin.partners.commissions', ['status' => 'paid']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'paid' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Settled / Paid</a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Ref Number</th>
                        <th class="py-3 px-4">Partner</th>
                        <th class="py-3 px-4">Referral Lead</th>
                        <th class="py-3 px-4">Base Volume</th>
                        <th class="py-3 px-4">Rate</th>
                        <th class="py-3 px-4">Commission</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($commissions as $comm)
                        <tr class="hover:bg-slate-50/75">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">#{{ $comm->reference_number }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900">{{ $comm->partner?->user?->name ?? 'Partner' }}</span>
                                <span class="block text-[11px] font-mono text-emerald-700">{{ $comm->partner?->referral_code }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-semibold text-slate-800">{{ $comm->referral?->client_name ?? $comm->referral?->client?->name ?? 'Client' }}</span>
                                <span class="block text-[11px] font-mono text-slate-400">#{{ $comm->referral?->reference_number }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono font-semibold text-slate-800">₹{{ number_format($comm->base_amount, 2) }}</td>
                            <td class="py-3 px-4">{{ $comm->commission_rate }}%</td>
                            <td class="py-3 px-4 font-mono font-bold text-emerald-600">₹{{ number_format($comm->commission_amount, 2) }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase
                                    {{ $comm->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ in_array($comm->status->value, ['approved', 'payable']) ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $comm->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $comm->status->value === 'rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $comm->status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1">
                                    @if($comm->status->value === 'pending')
                                        <form method="POST" action="{{ route('admin.partners.commissions.approve', $comm) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-[10px] font-bold">Approve</button>
                                        </form>
                                        <button onclick="document.getElementById('reject-modal-{{ $comm->id }}').classList.remove('hidden')" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded text-[10px] font-bold">Reject</button>
                                    @elseif($comm->status->value === 'approved')
                                        <form method="POST" action="{{ route('admin.partners.commissions.payable', $comm) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded text-[10px] font-bold">Mark Payable</button>
                                        </form>
                                    @elseif($comm->status->value === 'paid')
                                        <span class="text-[11px] font-mono text-slate-400">#{{ $comm->payout?->reference_number ?? 'Paid' }}</span>
                                    @endif
                                </div>

                                <!-- Reject Modal -->
                                <div id="reject-modal-{{ $comm->id }}" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
                                    <div class="bg-white rounded-xl max-w-sm w-full p-6 text-left shadow-2xl">
                                        <h3 class="font-bold text-slate-900 text-base mb-2">Reject Commission #{{ $comm->reference_number }}</h3>
                                        <form method="POST" action="{{ route('admin.partners.commissions.reject', $comm) }}">
                                            @csrf
                                            @method('PATCH')
                                            <label class="block text-xs font-bold text-slate-600 mb-1">Reason for Rejection *</label>
                                            <textarea name="rejection_reason" rows="3" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800 mb-4" placeholder="e.g. Duplicate referral or refunded deal..."></textarea>
                                            <div class="flex justify-end gap-2">
                                                <button type="button" onclick="document.getElementById('reject-modal-{{ $comm->id }}').classList.add('hidden')" class="px-3 py-1.5 bg-slate-100 text-slate-600 rounded text-xs font-bold">Cancel</button>
                                                <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs font-bold">Confirm Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center text-slate-400">No commission records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commissions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $commissions->links() }}
            </div>
        @endif
    </div>

    <!-- Create Manual Commission Modal -->
    <div id="new-commission-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-extrabold text-slate-900 text-lg">Record Partner Commission</h3>
                <button onclick="document.getElementById('new-commission-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.partners.commissions.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Converted Referral Lead *</label>
                    <select name="referral_id" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800">
                        <option value="">Select a Converted Referral...</option>
                        @foreach($referrals as $ref)
                            <option value="{{ $ref->id }}">
                                #{{ $ref->reference_number }} — {{ $ref->client_name ?? $ref->client?->name ?? 'Lead' }} (Partner: {{ $ref->partner?->user?->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Base Deal Volume (INR) *</label>
                    <input type="number" step="0.01" min="1" name="base_amount" required placeholder="50000.00" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800">
                    <p class="text-[11px] text-slate-400 mt-1">Commission will be computed automatically according to the partner's configured rate.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Internal Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800" placeholder="e.g. Verified milestone 1 payment for enterprise app project"></textarea>
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('new-commission-modal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold">Save Commission</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

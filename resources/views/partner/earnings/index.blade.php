@extends('layouts.partner')

@section('title', 'Commissions & Payouts')

@section('content')
<div class="space-y-8">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Commissions & Payouts</h1>
            <p class="text-sm text-slate-500">View real-time commission status, approvals, and bank/UPI payout ledger</p>
        </div>
        <div>
            <a href="{{ route('partner.profile') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Payout Settings
            </a>
        </div>
    </div>

    <!-- Financial Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Lifetime Earnings</span>
            <div class="text-2xl font-extrabold text-slate-900 mt-2 font-mono">₹{{ number_format($totalEarnings, 2) }}</div>
            <p class="text-xs text-slate-500 mt-1">Approved & paid commissions</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Pending Approval</span>
            <div class="text-2xl font-extrabold text-amber-600 mt-2 font-mono">₹{{ number_format($pendingAmount, 2) }}</div>
            <p class="text-xs text-slate-500 mt-1">Under admin verification</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">Ready for Payout</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-2 font-mono">₹{{ number_format($approvedAmount, 2) }}</div>
            <p class="text-xs text-slate-500 mt-1">Approved & payable balance</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm bg-gradient-to-br from-emerald-50/50 to-white">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-700">Total Disbursed</span>
            <div class="text-2xl font-extrabold text-emerald-800 mt-2 font-mono">₹{{ number_format($paidAmount, 2) }}</div>
            <p class="text-xs text-slate-500 mt-1">Settled to your bank/UPI</p>
        </div>
    </div>

    <!-- Section 1: Commissions History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Commissions Ledger</h3>
                <p class="text-xs text-slate-500">Detailed itemized list of commissions attributed to your account</p>
            </div>

            <!-- Filter Status -->
            <div class="flex items-center gap-2">
                <a href="{{ route('partner.earnings.index') }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ empty($statusFilter) ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">All</a>
                <a href="{{ route('partner.earnings.index', ['status' => 'pending']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Pending</a>
                <a href="{{ route('partner.earnings.index', ['status' => 'approved']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'approved' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Approved</a>
                <a href="{{ route('partner.earnings.index', ['status' => 'paid']) }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-colors {{ $statusFilter === 'paid' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Paid</a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-3 px-6">Reference</th>
                        <th class="py-3 px-6">Referral Lead</th>
                        <th class="py-3 px-6">Base Amount</th>
                        <th class="py-3 px-6">Rate</th>
                        <th class="py-3 px-6">Commission Amount</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Date</th>
                        <th class="py-3 px-6 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($commissions as $commission)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-6 font-mono text-xs font-bold text-slate-900">
                                #{{ $commission->reference_number }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="font-semibold text-slate-900">{{ $commission->referral?->maskedClientName() ?? 'Client' }}</span>
                                <span class="block text-xs font-mono text-slate-400">#{{ $commission->referral?->reference_number }}</span>
                            </td>
                            <td class="py-3.5 px-6 font-mono text-slate-800 font-semibold">
                                ₹{{ number_format($commission->base_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $commission->commission_rate }}%
                            </td>
                            <td class="py-3.5 px-6 font-mono font-bold text-emerald-600">
                                ₹{{ number_format($commission->commission_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $commission->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ in_array($commission->status->value, ['approved', 'payable']) ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $commission->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $commission->status->value === 'rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $commission->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $commission->created_at->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-6 text-right">
                                <a href="{{ route('partner.earnings.show', $commission) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800">
                                    View &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-6 text-center text-slate-400">
                                No commission records found.
                            </td>
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

    <!-- Section 2: Payouts History -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900 text-lg">Disbursement & Payout History</h3>
                <p class="text-xs text-slate-500">Direct settlements deposited to your registered bank account or UPI ID</p>
            </div>
            <span class="text-xs font-mono font-bold bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full border border-emerald-200">
                Method: {{ strtoupper($partner->payout_method) }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-3 px-6">Payout Reference</th>
                        <th class="py-3 px-6">Amount Disbursed</th>
                        <th class="py-3 px-6">Method</th>
                        <th class="py-3 px-6">Transaction Ref</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Disbursed Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($payouts as $payout)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-6 font-mono text-xs font-bold text-slate-900">
                                #{{ $payout->reference_number }}
                            </td>
                            <td class="py-3.5 px-6 font-mono font-extrabold text-emerald-700">
                                ₹{{ number_format($payout->amount, 2) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs uppercase font-semibold text-slate-600">
                                {{ $payout->payment_method }}
                            </td>
                            <td class="py-3.5 px-6 font-mono text-xs text-slate-500">
                                {{ $payout->transaction_reference ?? '—' }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $payout->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $payout->status->value === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $payout->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $payout->status->value === 'failed' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $payout->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $payout->paid_at ? $payout->paid_at->format('M d, Y h:i A') : $payout->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-slate-400 text-xs">
                                No disbursement payouts processed yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payouts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $payouts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

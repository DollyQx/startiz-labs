@extends('layouts.partner')

@section('title', 'Commission Details — #' . $commission->reference_number)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Back & Status -->
    <div class="flex items-center justify-between">
        <a href="{{ route('partner.earnings.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Earnings Ledger
        </a>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
            {{ $commission->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
            {{ in_array($commission->status->value, ['approved', 'payable']) ? 'bg-blue-100 text-blue-800' : '' }}
            {{ $commission->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
            {{ $commission->status->value === 'rejected' ? 'bg-rose-100 text-rose-800' : '' }}
        ">
            {{ $commission->status->label() }}
        </span>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="border-b border-slate-100 pb-6 mb-6">
            <span class="text-xs font-mono font-bold text-emerald-600 uppercase tracking-wider">Commission Voucher</span>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mt-1">
                <h1 class="text-2xl font-extrabold text-slate-900">#{{ $commission->reference_number }}</h1>
                <div class="text-3xl font-extrabold text-emerald-600 font-mono">
                    ₹{{ number_format($commission->commission_amount, 2) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 space-y-3 text-sm">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400">Calculation Breakdown</h4>
                <div class="flex justify-between">
                    <span class="text-slate-500">Base Deal Volume:</span>
                    <span class="font-mono font-bold text-slate-800">₹{{ number_format($commission->base_amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Commission Rate:</span>
                    <span class="font-bold text-slate-800">{{ $commission->commission_rate }}%</span>
                </div>
                <div class="border-t border-slate-200 pt-2 flex justify-between">
                    <span class="font-semibold text-slate-700">Net Earned:</span>
                    <span class="font-mono font-extrabold text-emerald-600">₹{{ number_format($commission->commission_amount, 2) }}</span>
                </div>
            </div>

            <div class="bg-slate-50 rounded-xl p-5 border border-slate-100 space-y-3 text-sm">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400">Attribution & Dates</h4>
                <div class="flex justify-between">
                    <span class="text-slate-500">Referral Code:</span>
                    <span class="font-mono font-bold text-emerald-700">{{ $commission->referral?->referral_code ?? 'STZ-REF' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Lead File:</span>
                    <span class="font-semibold text-slate-800">#{{ $commission->referral?->reference_number ?? '—' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Created:</span>
                    <span class="text-slate-800">{{ $commission->created_at->format('M d, Y h:i A') }}</span>
                </div>
                @if($commission->approved_at)
                    <div class="flex justify-between">
                        <span class="text-slate-500">Approved Date:</span>
                        <span class="text-slate-800 font-semibold">{{ $commission->approved_at->format('M d, Y') }}</span>
                    </div>
                @endif
            </div>
        </div>

        @if($commission->payout)
            <div class="bg-emerald-50 rounded-xl p-5 border border-emerald-200">
                <h4 class="font-bold text-xs uppercase tracking-wider text-emerald-800 mb-2">Disbursement Details</h4>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                    <div>
                        <span class="text-emerald-600">Payout Reference:</span>
                        <div class="font-mono font-bold text-emerald-900 mt-0.5">#{{ $commission->payout->reference_number }}</div>
                    </div>
                    <div>
                        <span class="text-emerald-600">Method & Txn:</span>
                        <div class="font-semibold text-emerald-900 mt-0.5 uppercase">{{ $commission->payout->payment_method }} &bull; {{ $commission->payout->transaction_reference ?? 'Direct Settlement' }}</div>
                    </div>
                    <div>
                        <span class="text-emerald-600">Disbursed On:</span>
                        <div class="font-semibold text-emerald-900 mt-0.5">{{ $commission->payout->paid_at ? $commission->payout->paid_at->format('M d, Y') : 'Processing' }}</div>
                    </div>
                </div>
            </div>
        @endif

        @if($commission->notes)
            <div class="mt-6 pt-6 border-t border-slate-100">
                <h4 class="font-bold text-xs uppercase tracking-wider text-slate-400 mb-1">Notes</h4>
                <p class="text-xs text-slate-600">{{ $commission->notes }}</p>
            </div>
        @endif
    </div>
</div>
@endsection

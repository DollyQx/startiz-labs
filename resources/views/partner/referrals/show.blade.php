@extends('layouts.partner')

@section('title', 'Referral Details — #' . $referral->reference_number)

@section('content')
<div class="space-y-6">
    <!-- Back & Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('partner.referrals.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Referrals
        </a>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
            {{ $referral->status->value === 'converted' ? 'bg-emerald-100 text-emerald-800' : '' }}
            {{ $referral->status->value === 'contacted' ? 'bg-blue-100 text-blue-800' : '' }}
            {{ $referral->status->value === 'new' ? 'bg-amber-100 text-amber-800' : '' }}
            {{ $referral->status->value === 'lost' ? 'bg-rose-100 text-rose-800' : '' }}
        ">
            {{ $referral->status->label() }}
        </span>
    </div>

    <!-- Referral Overview Card -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-6 mb-6">
            <div>
                <span class="text-xs font-mono font-bold text-emerald-600 uppercase tracking-wider">Referral File</span>
                <h1 class="text-2xl font-extrabold text-slate-900 mt-1">#{{ $referral->reference_number }}</h1>
            </div>
            <div class="text-sm sm:text-right text-slate-500">
                <span>First Attributed:</span>
                <span class="font-bold text-slate-800 block sm:inline ml-1">{{ $referral->created_at->format('M d, Y h:i A') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Client / Contact</span>
                <div class="text-base font-bold text-slate-900 mt-1">{{ $referral->maskedClientName() }}</div>
                <div class="text-xs font-mono text-slate-500 mt-1">{{ $referral->maskedClientEmail() }}</div>
                <div class="text-xs text-slate-400 mt-1">Phone: {{ $referral->maskedClientPhone() }}</div>
            </div>

            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Referral Attribution</span>
                <div class="text-sm font-semibold text-slate-900 mt-1">Code: <span class="font-mono text-emerald-600 font-bold">{{ $referral->referral_code }}</span></div>
                <div class="text-xs text-slate-500 mt-1">Service: {{ $referral->service_requested ?? 'General Portfolio Consultation' }}</div>
                @if($referral->converted_at)
                    <div class="text-xs text-emerald-600 font-bold mt-1">Converted on {{ $referral->converted_at->format('M d, Y') }}</div>
                @endif
            </div>

            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Privacy Notice</span>
                <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                    Client contact details are obfuscated in compliance with privacy guidelines. Our sales and delivery teams handle direct client engagements.
                </p>
            </div>
        </div>
    </div>

    <!-- Commissions Generated on this Referral -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-900">Commissions for this Lead</h3>
                <p class="text-xs text-slate-500">Every project or invoice payment converted from this client</p>
            </div>
            <span class="text-xs font-mono font-bold bg-slate-100 px-3 py-1 rounded-full text-slate-700">
                {{ $commissions->count() }} Commission Records
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-3 px-6">Reference</th>
                        <th class="py-3 px-6">Deal Amount</th>
                        <th class="py-3 px-6">Rate</th>
                        <th class="py-3 px-6">Your Commission</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Payout Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($commissions as $comm)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 px-6 font-mono text-xs font-bold text-slate-900">
                                #{{ $comm->reference_number }}
                            </td>
                            <td class="py-3.5 px-6 font-mono font-semibold text-slate-800">
                                ₹{{ number_format($comm->base_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $comm->commission_rate }}%
                            </td>
                            <td class="py-3.5 px-6 font-mono font-bold text-emerald-600">
                                ₹{{ number_format($comm->commission_amount, 2) }}
                            </td>
                            <td class="py-3.5 px-6">
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $comm->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ in_array($comm->status->value, ['approved', 'payable']) ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $comm->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $comm->status->value === 'rejected' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $comm->status->label() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-6 text-xs text-slate-500">
                                {{ $comm->payout?->paid_at ? $comm->payout->paid_at->format('M d, Y') : '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 px-6 text-center text-slate-400 text-xs">
                                No commission records generated yet for this referral.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

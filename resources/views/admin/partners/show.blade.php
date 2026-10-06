@extends('layouts.admin')

@section('title', 'Partner Dossier — ' . $partner->referral_code)
@section('breadcrumb', 'Partner Details')

@section('content')
<div class="space-y-6">
    <!-- Top Nav -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.partners.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            &larr; Back to Partners Directory
        </a>
        <div class="flex items-center gap-2">
            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold
                {{ $partner->status->value === 'approved' ? 'bg-emerald-100 text-emerald-800' : '' }}
                {{ $partner->status->value === 'pending' ? 'bg-amber-100 text-amber-800' : '' }}
                {{ $partner->status->value === 'suspended' ? 'bg-rose-100 text-rose-800' : '' }}
                {{ $partner->status->value === 'rejected' ? 'bg-slate-100 text-slate-800' : '' }}
            ">
                {{ $partner->status->label() }}
            </span>
        </div>
    </div>

    <!-- Partner Profile Dossier Card -->
    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-100 pb-6 mb-6">
            <div>
                <span class="text-xs font-mono font-bold text-blue-600 uppercase">Affiliate Partner Profile</span>
                <h1 class="text-2xl font-black text-slate-900 mt-1">{{ $partner->user?->name ?? 'Unknown Partner' }}</h1>
                <p class="text-xs text-slate-500 mt-0.5">{{ $partner->user?->email }} &bull; {{ $partner->phone ?? 'No phone' }}</p>
            </div>

            <div class="flex items-center gap-4">
                <!-- Status Actions -->
                @if($partner->status->value === 'pending')
                    <form method="POST" action="{{ route('admin.partners.status', $partner) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">Approve Partner</button>
                    </form>
                    <form method="POST" action="{{ route('admin.partners.status', $partner) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="rejected">
                        <button type="submit" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-xs font-bold">Reject</button>
                    </form>
                @elseif($partner->status->value === 'approved')
                    <form method="POST" action="{{ route('admin.partners.status', $partner) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="suspended">
                        <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold">Suspend Account</button>
                    </form>
                @elseif($partner->status->value === 'suspended')
                    <form method="POST" action="{{ route('admin.partners.status', $partner) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="approved">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">Reactivate Account</button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 text-xs">
            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block">Referral Code</span>
                <span class="font-mono text-base font-black text-slate-900 mt-1 block select-all">{{ $partner->referral_code }}</span>
                <span class="text-[11px] text-slate-500 font-mono mt-0.5 block truncate">{{ $partner->referralUrl() }}</span>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block">Commission Rate</span>
                <form method="POST" action="{{ route('admin.partners.rate', $partner) }}" class="flex items-center gap-1.5 mt-1">
                    @csrf
                    @method('PATCH')
                    <input type="number" step="0.01" min="0" max="100" name="commission_rate" value="{{ $partner->commission_rate }}" class="w-20 bg-slate-50 border border-slate-300 rounded px-2 py-1 text-sm font-bold text-slate-900 focus:outline-none focus:ring-1 focus:ring-blue-500">
                    <span class="font-bold text-slate-600">%</span>
                    <button type="submit" class="px-2 py-1 bg-slate-900 text-white rounded text-[11px] font-bold">Save</button>
                </form>
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block">Company / Agency</span>
                <span class="font-semibold text-slate-800 text-sm mt-1 block">{{ $partner->company_name ?? 'Individual' }}</span>
                @if($partner->website)
                    <a href="{{ $partner->website }}" target="_blank" class="text-blue-600 hover:underline text-[11px] block mt-0.5">{{ $partner->website }}</a>
                @endif
            </div>

            <div>
                <span class="text-slate-400 font-bold uppercase tracking-wider block">Payout Preferences</span>
                <span class="font-bold text-slate-900 uppercase block mt-1">{{ $partner->payout_method }}</span>
                <div class="bg-slate-50 p-2 rounded mt-1 border border-slate-200 text-slate-700 font-mono text-[11px] break-all">
                    {{ $partner->payout_details ?? 'No details provided' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Referrals Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Attributed Referrals & Leads</h3>
            <span class="text-xs text-slate-400">Total: {{ $referrals->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                        <th class="py-2.5 px-4">Ref Number</th>
                        <th class="py-2.5 px-4">Lead / Client Name</th>
                        <th class="py-2.5 px-4">Email</th>
                        <th class="py-2.5 px-4">Status</th>
                        <th class="py-2.5 px-4">Attributed Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($referrals as $ref)
                        <tr>
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-900">#{{ $ref->reference_number }}</td>
                            <td class="py-2.5 px-4 font-semibold text-slate-800">{{ $ref->client_name ?? $ref->client?->name ?? 'Lead' }}</td>
                            <td class="py-2.5 px-4 text-slate-600">{{ $ref->client_email ?? $ref->client?->email ?? '—' }}</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $ref->status->value === 'converted' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-800' }}
                                ">
                                    {{ $ref->status->label() }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-slate-500">{{ $ref->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 px-4 text-center text-slate-400">No referrals attributed yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($referrals->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>

    <!-- Commissions Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Commissions History</h3>
            <span class="text-xs text-slate-400">Total: {{ $commissions->total() }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase">
                        <th class="py-2.5 px-4">Reference</th>
                        <th class="py-2.5 px-4">Base Deal</th>
                        <th class="py-2.5 px-4">Rate</th>
                        <th class="py-2.5 px-4">Commission</th>
                        <th class="py-2.5 px-4">Status</th>
                        <th class="py-2.5 px-4">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($commissions as $comm)
                        <tr>
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-900">#{{ $comm->reference_number }}</td>
                            <td class="py-2.5 px-4 font-mono">₹{{ number_format($comm->base_amount, 2) }}</td>
                            <td class="py-2.5 px-4">{{ $comm->commission_rate }}%</td>
                            <td class="py-2.5 px-4 font-mono font-bold text-emerald-600">₹{{ number_format($comm->commission_amount, 2) }}</td>
                            <td class="py-2.5 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase
                                    {{ $comm->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}
                                ">
                                    {{ $comm->status->label() }}
                                </span>
                            </td>
                            <td class="py-2.5 px-4 text-slate-500">{{ $comm->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 px-4 text-center text-slate-400">No commissions recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($commissions->hasPages())
            <div class="p-3 border-t border-slate-100">
                {{ $commissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

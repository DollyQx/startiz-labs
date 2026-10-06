@extends('layouts.partner')

@section('title', 'Referrals & Leads')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Referrals & Leads</h1>
            <p class="text-sm text-slate-500">Track all accounts attributed to your referral code <span class="font-mono font-bold text-emerald-600">{{ $partner->referral_code }}</span></p>
        </div>
        <div>
            <a href="{{ route('partner.links') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                Get Referral Links
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-3">
        <a href="{{ route('partner.referrals.index') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ empty($statusFilter) ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            All ({{ $counts['all'] }})
        </a>
        <a href="{{ route('partner.referrals.index', ['status' => 'new']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $statusFilter === 'new' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            New ({{ $counts['new'] }})
        </a>
        <a href="{{ route('partner.referrals.index', ['status' => 'contacted']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $statusFilter === 'contacted' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Contacted ({{ $counts['contacted'] }})
        </a>
        <a href="{{ route('partner.referrals.index', ['status' => 'converted']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $statusFilter === 'converted' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Converted ({{ $counts['converted'] }})
        </a>
        <a href="{{ route('partner.referrals.index', ['status' => 'lost']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition-colors {{ $statusFilter === 'lost' ? 'bg-rose-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
            Lost ({{ $counts['lost'] }})
        </a>
    </div>

    <!-- Referrals Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-slate-50/75 border-b border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-wider">
                        <th class="py-3.5 px-6">Reference</th>
                        <th class="py-3.5 px-6">Lead / Client</th>
                        <th class="py-3.5 px-6">Service Requested</th>
                        <th class="py-3.5 px-6">Status</th>
                        <th class="py-3.5 px-6">Attributed Date</th>
                        <th class="py-3.5 px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($referrals as $referral)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="py-4 px-6 font-mono text-xs font-bold text-slate-900">
                                #{{ $referral->reference_number }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $referral->maskedClientName() }}</div>
                                <div class="text-xs text-slate-400 font-mono">{{ $referral->maskedClientEmail() }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-600 text-xs">
                                {{ $referral->service_requested ?? 'General Consultation' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $referral->status->value === 'converted' ? 'bg-emerald-100 text-emerald-800' : '' }}
                                    {{ $referral->status->value === 'contacted' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $referral->status->value === 'new' ? 'bg-amber-100 text-amber-800' : '' }}
                                    {{ $referral->status->value === 'lost' ? 'bg-rose-100 text-rose-800' : '' }}
                                ">
                                    {{ $referral->status->label() }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-xs text-slate-500">
                                {{ $referral->created_at->format('M d, Y') }}
                                <span class="block text-[11px] text-slate-400">{{ $referral->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('partner.referrals.show', $referral) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800">
                                    Details &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 px-6 text-center text-slate-400">
                                No referrals found for the selected status.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($referrals->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

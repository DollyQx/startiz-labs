@extends('layouts.admin')

@section('title', 'Partner Payouts & Disbursements')
@section('breadcrumb', 'Partner Payouts')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Partner Payouts & Disbursements</h1>
            <p class="text-xs text-slate-500 mt-1">Batch disburse approved commissions and record transaction settlements</p>
        </div>
        <div>
            <button onclick="document.getElementById('new-payout-modal').classList.toggle('hidden')" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition-colors">
                + Process New Payout
            </button>
        </div>
    </div>

    <!-- Ready for Settlement Notice -->
    @if($payablePartners->isNotEmpty())
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                    {{ $payablePartners->count() }}
                </div>
                <div>
                    <h4 class="font-bold text-emerald-950 text-xs">Partners with Payable Commissions</h4>
                    <p class="text-[11px] text-emerald-800">There are {{ $payablePartners->count() }} partner(s) eligible for settlement right now.</p>
                </div>
            </div>
            <button onclick="document.getElementById('new-payout-modal').classList.remove('hidden')" class="px-3 py-1.5 bg-emerald-600 text-white rounded text-xs font-bold hover:bg-emerald-700">
                Disburse Now
            </button>
        </div>
    @endif

    <!-- Payouts Ledger Table -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900 text-sm">Disbursement History</h3>
            <span class="text-xs text-slate-400">Total: {{ $payouts->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">Payout Ref</th>
                        <th class="py-3 px-4">Partner</th>
                        <th class="py-3 px-4">Amount</th>
                        <th class="py-3 px-4">Method</th>
                        <th class="py-3 px-4">Txn Reference</th>
                        <th class="py-3 px-4">Processed By</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Disbursed Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($payouts as $payout)
                        <tr class="hover:bg-slate-50/75">
                            <td class="py-3 px-4 font-mono font-bold text-slate-900">#{{ $payout->reference_number }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.partners.show', $payout->partner) }}" class="font-bold text-blue-600 hover:underline">
                                    {{ $payout->partner?->user?->name ?? 'Partner' }}
                                </a>
                                <span class="block text-[11px] font-mono text-emerald-700">{{ $payout->partner?->referral_code }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono font-extrabold text-emerald-700 text-sm">
                                ₹{{ number_format($payout->amount, 2) }}
                            </td>
                            <td class="py-3 px-4 uppercase font-bold text-slate-600">
                                {{ $payout->payment_method }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500">
                                {{ $payout->transaction_reference ?? '—' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $payout->processedBy?->name ?? 'System' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="inline-block px-2.5 py-0.5 rounded-full font-bold text-[10px] uppercase
                                    {{ $payout->status->value === 'paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}
                                ">
                                    {{ $payout->status->label() }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $payout->paid_at ? $payout->paid_at->format('M d, Y') : $payout->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 px-4 text-center text-slate-400">No payout records found.</td>
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

    <!-- Process Payout Modal -->
    <div id="new-payout-modal" class="hidden fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <h3 class="font-extrabold text-slate-900 text-lg">Process Partner Payout</h3>
                <button onclick="document.getElementById('new-payout-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">✕</button>
            </div>

            @if($payablePartners->isEmpty())
                <p class="text-xs text-slate-500 py-6 text-center">There are currently no approved commissions waiting for payout.</p>
            @else
                <form method="POST" action="{{ route('admin.partners.payouts.store') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Select Partner *</label>
                        <select name="partner_id" id="modal-partner-select" required onchange="renderCommissionsForPartner(this.value)" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800">
                            <option value="">Select an eligible partner...</option>
                            @foreach($payablePartners as $p)
                                <option value="{{ $p->id }}" data-method="{{ $p->payout_method }}" data-details="{{ $p->payout_details }}">
                                    {{ $p->user?->name }} ({{ $p->referral_code }}) — {{ $p->commissions->count() }} eligible commissions
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="partner-bank-info" class="hidden bg-slate-50 p-3 rounded-lg border border-slate-200 text-xs">
                        <span class="font-bold text-slate-700 block">Payout Destination:</span>
                        <div id="partner-bank-details" class="font-mono text-slate-600 mt-1 break-all"></div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Commissions to Settle *</label>
                        <div id="commissions-checkbox-list" class="max-h-48 overflow-y-auto border border-slate-200 rounded-lg p-3 space-y-2 text-xs bg-slate-50">
                            <span class="text-slate-400">Please choose a partner above first.</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Payment Method *</label>
                            <select name="payment_method" required class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800">
                                <option value="bank_transfer">Bank Transfer (NEFT/IMPS)</option>
                                <option value="upi">UPI Settlement</option>
                                <option value="manual">Manual / Cheque</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Transaction Ref / UTR</label>
                            <input type="text" name="transaction_reference" placeholder="UTR123456789" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-600 mb-1">Internal Notes</label>
                        <textarea name="notes" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-xs text-slate-800" placeholder="e.g. Month-end batch disbursement"></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('new-payout-modal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg text-xs font-bold">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold">Process Settlement</button>
                    </div>
                </form>

                <script>
                    const partnersData = @json($payablePartners->values());

                    function renderCommissionsForPartner(partnerId) {
                        const list = document.getElementById('commissions-checkbox-list');
                        const infoBox = document.getElementById('partner-bank-info');
                        const infoDetails = document.getElementById('partner-bank-details');
                        
                        list.innerHTML = '';
                        if (!partnerId) {
                            list.innerHTML = '<span class="text-slate-400">Please choose a partner above first.</span>';
                            infoBox.classList.add('hidden');
                            return;
                        }

                        const partner = partnersData.find(p => p.id == partnerId);
                        if (!partner) return;

                        infoBox.classList.remove('hidden');
                        infoDetails.textContent = `${(partner.payout_method || 'N/A').toUpperCase()}: ${partner.payout_details || 'No details'}`;

                        if (partner.commissions && partner.commissions.length > 0) {
                            partner.commissions.forEach(c => {
                                const row = document.createElement('label');
                                row.className = 'flex items-center gap-2 p-1.5 hover:bg-white rounded cursor-pointer';
                                row.innerHTML = `
                                    <input type="checkbox" name="commission_ids[]" value="${c.id}" checked class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="font-mono font-bold">#${c.reference_number}</span>
                                    <span class="text-slate-500">Base: ₹${Number(c.base_amount).toFixed(2)}</span>
                                    <span class="font-bold text-emerald-700 ml-auto">₹${Number(c.commission_amount).toFixed(2)}</span>
                                `;
                                list.appendChild(row);
                            });
                        } else {
                            list.innerHTML = '<span class="text-slate-400">No payable commissions found for this partner.</span>';
                        }
                    }
                </script>
            @endif
        </div>
    </div>
</div>
@endsection

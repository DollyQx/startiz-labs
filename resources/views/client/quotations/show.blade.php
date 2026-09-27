@extends('layouts.client')

@section('title', 'Quotation #' . $quotation->reference_number)

@section('content')
<div class="space-y-6">

    <!-- Top Navigation Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('client.quotations.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Quotations Directory
        </a>
    </div>

    <!-- Header Workspace Banner -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs font-bold bg-blue-50 text-blue-700 px-3 py-1 rounded-md border border-blue-100 uppercase tracking-wide">
                        {{ $quotation->reference_number }}
                    </span>
                    @php
                        $badgeStyles = match($quotation->status->value ?? $quotation->status) {
                            'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'sent', 'viewed' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'expired' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'cancelled' => 'bg-slate-100 text-slate-600 border-slate-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200'
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyles }}">
                        {{ is_object($quotation->status) ? $quotation->status->label() : ucfirst($quotation->status) }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Commercial Quotation Proposal</h1>
                <p class="text-xs text-slate-500">
                    Project: <span class="font-bold text-slate-700">{{ $quotation->project->title ?? 'General Software Engagement' }}</span>
                </p>
            </div>

            <!-- Client Action Buttons (if in acceptable state) -->
            @if(in_array($quotation->status->value ?? $quotation->status, ['sent', 'viewed']))
                <div class="flex items-center gap-3">
                    <!-- Reject Button -->
                    <button onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="px-4 py-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Reject Quotation
                    </button>

                    <!-- Accept Button -->
                    <button onclick="document.getElementById('accept-modal').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20 transition-all flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Accept Proposal
                    </button>
                </div>
            @endif
        </div>

        <!-- Issue & Validity Metadata -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 bg-slate-50 p-4 rounded-xl border border-slate-200/70 text-xs">
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Issue Date</span>
                <span class="font-bold text-slate-800 text-sm">{{ $quotation->issue_date ? $quotation->issue_date->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Valid Until</span>
                <span class="font-bold text-slate-800 text-sm">{{ $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : 'N/A' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Prepared For</span>
                <span class="font-bold text-slate-800 text-sm">{{ $quotation->client->name ?? 'Client' }}</span>
            </div>
            <div>
                <span class="block text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Total Investment</span>
                <span class="font-mono font-extrabold text-blue-600 text-sm">₹{{ number_format((float)$quotation->total, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Line Items Table -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden p-6 space-y-4">
        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Services & Scope Breakdown</h2>

        <div class="border border-slate-200 rounded-xl overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-200/80 text-xs font-bold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">#</th>
                        <th class="py-3.5 px-4">Service / Description</th>
                        <th class="py-3.5 px-4 text-center">Qty</th>
                        <th class="py-3.5 px-4 text-right">Unit Price</th>
                        <th class="py-3.5 px-4 text-right">Discount</th>
                        <th class="py-3.5 px-4 text-right">Tax</th>
                        <th class="py-3.5 px-4 text-right">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($quotation->items as $index => $item)
                        @php
                            $lineSubtotal = $item->quantity * $item->unit_price;
                            $lineTotal = max(0, $lineSubtotal - $item->discount + $item->tax);
                        @endphp
                        <tr>
                            <td class="py-4 px-4 font-mono text-xs text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->title ?? 'Service Item' }}</span>
                                @if($item->description)
                                    <span class="text-xs text-slate-500 mt-0.5 block leading-relaxed">{{ $item->description }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-4 text-center font-mono text-slate-800">{{ $item->quantity }}</td>
                            <td class="py-4 px-4 text-right font-mono text-slate-800">₹{{ number_format((float)$item->unit_price, 2) }}</td>
                            <td class="py-4 px-4 text-right font-mono text-emerald-600">
                                {{ $item->discount > 0 ? '₹' . number_format((float)$item->discount, 2) : '—' }}
                            </td>
                            <td class="py-4 px-4 text-right font-mono text-slate-500">
                                {{ $item->tax > 0 ? '₹' . number_format((float)$item->tax, 2) : '—' }}
                            </td>
                            <td class="py-4 px-4 text-right font-mono font-bold text-slate-900">
                                ₹{{ number_format((float)$lineTotal, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-sm">No line items specified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Financial Summary Breakdown Box -->
        <div class="flex justify-end pt-4">
            <div class="w-full max-w-sm bg-slate-50 p-5 rounded-xl border border-slate-200/80 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600">
                    <span>Subtotal:</span>
                    <span class="font-mono font-medium">₹{{ number_format((float)$quotation->subtotal, 2) }}</span>
                </div>
                @if((float)$quotation->discount > 0)
                    <div class="flex justify-between text-emerald-600">
                        <span>Discount:</span>
                        <span class="font-mono font-medium">-₹{{ number_format((float)$quotation->discount, 2) }}</span>
                    </div>
                @endif
                @if((float)$quotation->tax > 0)
                    <div class="flex justify-between text-slate-600">
                        <span>Tax / GST:</span>
                        <span class="font-mono font-medium">+₹{{ number_format((float)$quotation->tax, 2) }}</span>
                    </div>
                @endif
                <div class="border-t border-slate-200 pt-3 flex justify-between items-center text-base font-extrabold text-slate-900">
                    <span>Total Investment:</span>
                    <span class="font-mono text-xl text-blue-600">₹{{ number_format((float)$quotation->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Notes & Terms Section -->
    @if($quotation->notes || $quotation->terms)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($quotation->notes)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Scope Notes</h3>
                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $quotation->notes }}</p>
                </div>
            @endif

            @if($quotation->terms)
                <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-2">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terms & Conditions</h3>
                    <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line">{{ $quotation->terms }}</p>
                </div>
            @endif
        </div>
    @endif

</div>

<!-- Acceptance Modal -->
<div id="accept-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-6 shadow-2xl border border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Accept Quotation Proposal?</h3>
                <p class="text-xs text-slate-500">Confirm your acceptance of proposal #{{ $quotation->reference_number }}.</p>
            </div>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200/60">
            By accepting, you agree to the scope of work and financial terms of <strong class="text-slate-900">₹{{ number_format((float)$quotation->total, 2) }}</strong>. Our team will initiate the next delivery phase and invoice setup.
        </p>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" onclick="document.getElementById('accept-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                Cancel
            </button>
            <form action="{{ route('client.quotations.accept', $quotation->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-500/20">
                    Yes, Accept Proposal
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Rejection Modal -->
<div id="reject-modal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-6 shadow-2xl border border-slate-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Reject Quotation Proposal?</h3>
                <p class="text-xs text-slate-500">Confirm rejection of proposal #{{ $quotation->reference_number }}.</p>
            </div>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-200/60">
            Are you sure you wish to reject this quotation? You can contact your account representative to discuss modifications or request a revised proposal.
        </p>

        <div class="flex items-center justify-end gap-3 pt-2">
            <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                Cancel
            </button>
            <form action="{{ route('client.quotations.reject', $quotation->id) }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-500/20">
                    Confirm Rejection
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

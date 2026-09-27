@extends('layouts.admin')

@section('title', 'Quotation ' . $quotation->reference_number)
@section('breadcrumb', 'Quotation Detail')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-600 text-white font-black flex items-center justify-center text-lg shadow-md">
                QUO
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight font-mono">{{ $quotation->reference_number }}</h1>
                    @php
                        $badgeStyle = match($quotation->status->value) {
                            'draft' => 'bg-slate-100 text-slate-700 border-slate-200',
                            'sent' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'viewed' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'accepted' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                            'expired' => 'bg-amber-50 text-amber-700 border-amber-200',
                            'cancelled' => 'bg-slate-100 text-slate-500 border-slate-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyle }}">
                        {{ $quotation->status->label() }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-1">
                    Issued on <span class="font-semibold text-slate-700 font-mono">{{ $quotation->issue_date ? $quotation->issue_date->format('M d, Y') : 'N/A' }}</span> &bull;
                    Valid until <span class="font-semibold text-slate-700 font-mono">{{ $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : 'N/A' }}</span>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Create Invoice Action -->
            <a href="{{ route('admin.invoices.create', ['quotation_id' => $quotation->id]) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Create Invoice
            </a>

            <!-- Status Transition Form -->
            <form action="{{ route('admin.quotations.status', $quotation->id) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:bg-white focus:outline-none">
                    <option value="" disabled selected>Update Status...</option>
                    @foreach($statuses as $st)
                        @if($st->value !== $quotation->status->value)
                            <option value="{{ $st->value }}">Mark as {{ $st->label() }}</option>
                        @endif
                    @endforeach
                </select>
            </form>

            @if(!in_array($quotation->status->value, ['accepted', 'cancelled']))
                <a href="{{ route('admin.quotations.edit', $quotation->id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Quotation
                </a>
            @endif

            <a href="{{ route('admin.quotations.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-colors">
                Back to List
            </a>
        </div>
    </div>

    <!-- Letterhead Business Document Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden p-8 space-y-8">
        <!-- Document Header / Letterhead -->
        <div class="flex flex-col md:flex-row justify-between border-b border-slate-200 pb-8 gap-6">
            <div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded bg-blue-600 text-white font-black flex items-center justify-center text-base">
                        STZ
                    </div>
                    <span class="font-extrabold text-xl text-slate-900 tracking-tight">Startiz Labs</span>
                </div>
                <div class="text-xs text-slate-500 mt-2 space-y-0.5">
                    <p>Enterprise Software Development & Digital Solutions</p>
                    <p>Email: contact@gmcodelab.com</p>
                    <p>Web: www.gmcodelab.com</p>
                </div>
            </div>

            <div class="text-left md:text-right text-xs text-slate-600 space-y-1">
                <h2 class="text-lg font-black text-slate-900 uppercase tracking-wide">Quotation Proposal</h2>
                <p><span class="font-semibold text-slate-500">Reference:</span> <span class="font-mono font-bold text-blue-600">{{ $quotation->reference_number }}</span></p>
                <p><span class="font-semibold text-slate-500">Date:</span> <span class="font-mono">{{ $quotation->issue_date ? $quotation->issue_date->format('M d, Y') : 'N/A' }}</span></p>
                <p><span class="font-semibold text-slate-500">Valid Until:</span> <span class="font-mono">{{ $quotation->valid_until ? $quotation->valid_until->format('M d, Y') : 'N/A' }}</span></p>
            </div>
        </div>

        <!-- Client & Context Metadata -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50 p-6 rounded-xl border border-slate-200">
            <!-- Prepared For Client -->
            <div class="space-y-1 text-xs">
                <h3 class="font-bold text-slate-400 uppercase tracking-wider text-[11px] mb-2">Prepared For Client</h3>
                <p class="text-sm font-extrabold text-slate-900">{{ $quotation->client->name ?? 'N/A' }}</p>
                @if($quotation->client?->clientProfile?->company_name)
                    <p class="font-semibold text-slate-700">{{ $quotation->client->clientProfile->company_name }}</p>
                @endif
                <p class="text-slate-600">{{ $quotation->client->email ?? '' }}</p>
                @if($quotation->client?->clientProfile?->phone)
                    <p class="text-slate-600">Phone: {{ $quotation->client->clientProfile->phone }}</p>
                @endif
            </div>

            <!-- Project Context -->
            <div class="space-y-1 text-xs">
                <h3 class="font-bold text-slate-400 uppercase tracking-wider text-[11px] mb-2">Project & Lead Context</h3>
                @if($quotation->project)
                    <p class="text-sm font-bold text-slate-900">
                        <a href="{{ route('admin.projects.show', $quotation->project->id) }}" class="text-blue-600 hover:underline">
                            Project: {{ $quotation->project->title }}
                        </a>
                    </p>
                    <p class="font-mono text-slate-500">Ref: {{ $quotation->project->reference_number }}</p>
                    <p class="text-slate-600">Status: <span class="font-semibold capitalize">{{ str_replace('_', ' ', $quotation->project->status->value) }}</span></p>
                @elseif($quotation->lead)
                    <p class="text-sm font-bold text-slate-900">
                        <a href="{{ route('admin.leads.show', $quotation->lead->id) }}" class="text-blue-600 hover:underline">
                            Lead: {{ $quotation->lead->name }}
                        </a>
                    </p>
                    <p class="font-mono text-slate-500">Ref: {{ $quotation->lead->reference_number }}</p>
                @else
                    <p class="text-slate-500 italic">Direct Quotation (No linked project or lead)</p>
                @endif
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="space-y-3">
            <h3 class="font-bold text-sm text-slate-900">Proposed Scope & Line Items</h3>
            <div class="overflow-x-auto rounded-xl border border-slate-200">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-4 py-3 w-12 text-center">#</th>
                            <th class="px-4 py-3">Description / Service</th>
                            <th class="px-4 py-3 w-20 text-center">Qty</th>
                            <th class="px-4 py-3 w-28 text-right">Unit Price</th>
                            <th class="px-4 py-3 w-24 text-right">Discount</th>
                            <th class="px-4 py-3 w-24 text-right">Tax</th>
                            <th class="px-4 py-3 w-32 text-right">Line Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-slate-700">
                        @forelse($quotation->items as $index => $item)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-4 py-3.5 text-center font-mono text-xs text-slate-400">
                                    {{ $item->sequence_order ?? ($index + 1) }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <div class="font-semibold text-slate-900">{{ $item->description }}</div>
                                    @if($item->service)
                                        <div class="text-xs text-blue-600 font-medium">Service: {{ $item->service->name }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-xs font-bold text-slate-800">
                                    {{ number_format($item->quantity, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-xs text-slate-800">
                                    ₹{{ number_format($item->unit_price, 2) }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-xs text-rose-600">
                                    {{ $item->discount > 0 ? '-₹' . number_format($item->discount, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-xs text-slate-600">
                                    {{ $item->tax > 0 ? '+₹' . number_format($item->tax, 2) : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-right font-mono text-sm font-extrabold text-slate-900">
                                    ₹{{ number_format($item->line_total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-slate-400 text-sm">
                                    No line items added to this quotation.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Summary Breakdown -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-6 pt-4 border-t border-slate-200">
            <!-- Left Side: Notes & Terms -->
            <div class="flex-1 space-y-4 text-xs">
                @if($quotation->notes)
                    <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                        <h4 class="font-bold text-blue-900 uppercase tracking-wider text-[10px] mb-1">Proposal Notes</h4>
                        <p class="text-slate-700 whitespace-pre-line">{{ $quotation->notes }}</p>
                    </div>
                @endif

                @if($quotation->terms)
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                        <h4 class="font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-1">Payment Terms & Conditions</h4>
                        <p class="text-slate-600 whitespace-pre-line">{{ $quotation->terms }}</p>
                    </div>
                @endif
            </div>

            <!-- Right Side: Totals Card -->
            <div class="w-full sm:w-80 bg-slate-900 text-white p-6 rounded-2xl shadow-md space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-800 pb-2">Financial Summary</h4>
                <div class="flex justify-between text-xs text-slate-300">
                    <span>Subtotal:</span>
                    <span class="font-mono font-bold text-white">₹{{ number_format($quotation->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs text-slate-300">
                    <span>Discount:</span>
                    <span class="font-mono font-bold text-rose-400">-₹{{ number_format($quotation->discount, 2) }}</span>
                </div>
                <div class="flex justify-between text-xs text-slate-300">
                    <span>Taxes:</span>
                    <span class="font-mono font-bold text-emerald-400">+₹{{ number_format($quotation->tax, 2) }}</span>
                </div>
                <div class="flex justify-between text-base font-black text-white pt-3 border-t border-slate-800">
                    <span>Grand Total:</span>
                    <span class="font-mono text-blue-400">₹{{ number_format($quotation->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Audit Log Timeline -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Quotation Activity & Audit History</h3>

        <div class="flow-root">
            <ul class="-mb-8">
                @forelse($activityLogs as $log)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                                <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-slate-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center ring-8 ring-white">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-xs text-slate-900 font-semibold">
                                            {{ $log->description ?? $log->action }}
                                            <span class="text-slate-500 font-normal">by {{ $log->actor->name ?? 'System' }}</span>
                                        </p>
                                        @if($log->metadata)
                                            <div class="text-[11px] text-slate-500 font-mono mt-0.5">
                                                {{ json_encode($log->metadata) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="whitespace-nowrap text-right text-[11px] text-slate-400 font-mono">
                                        {{ $log->created_at ? $log->created_at->diffForHumans() : '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-xs text-slate-500 italic py-2">No activity recorded for this quotation yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

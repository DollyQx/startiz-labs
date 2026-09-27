@extends('layouts.admin')

@section('title', 'Admin CRM Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">CRM Dashboard Overview</h1>
            <p class="text-sm text-slate-500 mt-1">Welcome back, {{ $adminUser->name }}. Overview of client accounts and lead pipelines.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.leads.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold text-sm shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                New Lead Proposal
            </a>
        </div>
    </div>

    <!-- Top Summary Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5">
        <!-- Total Clients -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Clients</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($stats['total_clients']) }}</div>
                <p class="text-[11px] text-slate-500 mt-1"><span class="text-emerald-600 font-semibold">{{ $stats['active_clients'] }}</span> active accounts</p>
            </div>
        </div>

        <!-- Total Leads -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Leads</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($stats['total_leads']) }}</div>
                <p class="text-[11px] text-slate-500 mt-1"><span class="text-blue-600 font-semibold">{{ $stats['new_leads'] }}</span> new inquiries</p>
            </div>
        </div>

        <!-- Total Projects -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Projects</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($stats['total_projects']) }}</div>
                <p class="text-[11px] text-slate-500 mt-1"><span class="text-blue-600 font-semibold">{{ $stats['active_projects'] }}</span> active projects</p>
            </div>
        </div>

        <!-- Quotations (Phase 6A) -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Quotations</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($stats['total_quotations']) }}</div>
                <p class="text-[11px] text-slate-500 mt-1"><span class="text-emerald-600 font-semibold">{{ $stats['accepted_quotations'] }}</span> accepted proposals</p>
            </div>
        </div>

        <!-- Overdue Projects -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Overdue Projects</span>
                <div class="w-8 h-8 rounded-lg {{ $stats['overdue_projects'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-slate-50 text-slate-500' }} flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold {{ $stats['overdue_projects'] > 0 ? 'text-rose-600' : 'text-slate-900' }}">{{ number_format($stats['overdue_projects']) }}</div>
                <p class="text-[11px] text-slate-500 mt-1"><span class="text-emerald-600 font-semibold">{{ $stats['completed_projects'] }}</span> completed</p>
            </div>
        </div>
    </div>

    <!-- Financial Collection Overview Section (Phase 6D) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Financial Summary & Revenue Overview
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Authoritative invoice balances and payment collection statistics.</p>
            </div>

            <!-- Financial Period Filter Form -->
            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                <label for="period" class="text-xs font-semibold text-slate-500 uppercase">Period:</label>
                <select id="period" name="period" onchange="this.form.submit()" class="px-3 py-1.5 text-xs font-semibold rounded-xl border border-slate-200 focus:border-blue-500 text-slate-800 bg-slate-50">
                    <option value="all_time" {{ $stats['selected_period'] === 'all_time' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ $stats['selected_period'] === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="this_week" {{ $stats['selected_period'] === 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ $stats['selected_period'] === 'this_month' ? 'selected' : '' }}>This Month</option>
                    <option value="this_year" {{ $stats['selected_period'] === 'this_year' ? 'selected' : '' }}>This Year</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Total Collected Card -->
            <div class="bg-gradient-to-br from-emerald-50 to-teal-50/50 p-5 rounded-xl border border-emerald-200/70">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Total Collected</span>
                <div class="text-2xl font-extrabold text-emerald-900 mt-2 font-mono">
                    ₹{{ number_format((float)$stats['total_collected'], 2) }}
                </div>
                <p class="text-[11px] text-emerald-700 mt-1">Verified payment credits ({{ ucfirst(str_replace('_', ' ', $stats['selected_period'])) }})</p>
            </div>

            <!-- Outstanding Balance Card -->
            <div class="bg-gradient-to-br from-amber-50 to-orange-50/50 p-5 rounded-xl border border-amber-200/70">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-800">Outstanding Balance</span>
                <div class="text-2xl font-extrabold text-amber-900 mt-2 font-mono">
                    ₹{{ number_format((float)$stats['outstanding_balance'], 2) }}
                </div>
                <p class="text-[11px] text-amber-700 mt-1">Remaining balance across active invoices</p>
            </div>

            <!-- Overdue Balance Card -->
            <div class="bg-gradient-to-br from-rose-50 to-pink-50/50 p-5 rounded-xl border border-rose-200/70">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-800">Overdue Invoices</span>
                <div class="text-2xl font-extrabold text-rose-900 mt-2 font-mono">
                    ₹{{ number_format((float)$stats['overdue_balance'], 2) }}
                </div>
                <p class="text-[11px] text-rose-700 mt-1"><span class="font-bold">{{ $stats['overdue_invoices'] }}</span> overdue invoice(s)</p>
            </div>

            <!-- Invoice Status Distribution Card -->
            <div class="bg-slate-50 p-5 rounded-xl border border-slate-200/80 space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block">Invoice Status Breakdown</span>
                <div class="grid grid-cols-3 gap-1 text-center pt-1">
                    <div class="bg-white p-2 rounded-lg border border-slate-200/60">
                        <span class="text-[10px] font-bold text-emerald-600 block uppercase">Paid</span>
                        <span class="text-base font-extrabold text-slate-900">{{ $stats['paid_invoices'] }}</span>
                    </div>
                    <div class="bg-white p-2 rounded-lg border border-slate-200/60">
                        <span class="text-[10px] font-bold text-amber-600 block uppercase">Partial</span>
                        <span class="text-base font-extrabold text-slate-900">{{ $stats['partially_paid_invoices'] }}</span>
                    </div>
                    <div class="bg-white p-2 rounded-lg border border-slate-200/60">
                        <span class="text-[10px] font-bold text-slate-500 block uppercase">Unpaid</span>
                        <span class="text-base font-extrabold text-slate-900">{{ $stats['unpaid_invoices'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lead Pipeline Breakdown Summary Bar -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Lead Pipeline Stage Breakdown</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 text-center">
            <div class="p-3 rounded-lg bg-slate-50 border border-slate-200">
                <span class="text-xs font-semibold text-slate-500 block">New</span>
                <span class="text-xl font-bold text-slate-900 mt-1 block">{{ $stats['new_leads'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-blue-50 border border-blue-100">
                <span class="text-xs font-semibold text-blue-600 block">Contacted</span>
                <span class="text-xl font-bold text-blue-900 mt-1 block">{{ $stats['contacted_leads'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-indigo-50 border border-indigo-100">
                <span class="text-xs font-semibold text-indigo-600 block">Qualified</span>
                <span class="text-xl font-bold text-indigo-900 mt-1 block">{{ $stats['qualified_leads'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-purple-50 border border-purple-100">
                <span class="text-xs font-semibold text-purple-600 block">Quote Sent</span>
                <span class="text-xl font-bold text-purple-900 mt-1 block">{{ $stats['quotation_sent_leads'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-amber-50 border border-amber-100">
                <span class="text-xs font-semibold text-amber-600 block">Negotiation</span>
                <span class="text-xl font-bold text-amber-900 mt-1 block">{{ $stats['negotiation_leads'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-emerald-50 border border-emerald-100">
                <span class="text-xs font-semibold text-emerald-600 block">Won</span>
                <span class="text-xl font-bold text-emerald-900 mt-1 block">{{ $stats['won_leads'] }}</span>
            </div>
            <div class="p-3 rounded-lg bg-rose-50 border border-rose-100">
                <span class="text-xs font-semibold text-rose-600 block">Lost</span>
                <span class="text-xl font-bold text-rose-900 mt-1 block">{{ $stats['lost_leads'] }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Delivery Projects & Quotations Section (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Delivery Projects -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Recent Projects</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Active delivery projects overview</p>
                </div>
                <a href="{{ route('admin.projects.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Reference</th>
                            <th class="px-5 py-3">Project Title</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @forelse($recentProjects as $proj)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono font-semibold text-blue-600">
                                    {{ $proj->reference_number }}
                                </td>
                                <td class="px-5 py-3 font-semibold text-slate-900">
                                    {{ $proj->title }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full border bg-slate-100 text-slate-800 border-slate-200">
                                        {{ $proj->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.projects.show', $proj->id) }}" class="text-xs font-semibold text-blue-600 hover:underline">Workspace</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-xs text-slate-500">
                                    No projects created yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Quotations -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Recent Quotations</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Commercial proposals and estimates</p>
                </div>
                <a href="{{ route('admin.quotations.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Reference</th>
                            <th class="px-5 py-3">Client</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @forelse($recentQuotations as $quo)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono font-semibold text-blue-600">
                                    {{ $quo->reference_number }}
                                </td>
                                <td class="px-5 py-3 font-semibold text-slate-900">
                                    {{ $quo->client->name ?? 'N/A' }}
                                </td>
                                <td class="px-5 py-3 font-mono font-bold text-slate-900">
                                    ₹{{ number_format((float)$quo->total, 2) }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full border bg-slate-100 text-slate-800 border-slate-200">
                                        {{ $quo->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.quotations.show', $quo->id) }}" class="text-xs font-semibold text-blue-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-500">
                                    No quotations generated yet. <a href="{{ route('admin.quotations.create') }}" class="text-blue-600 font-semibold hover:underline">Create quotation</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Invoices (Phase 6B) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Recent Invoices</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Commercial invoices and payment collection status</p>
                </div>
                <a href="{{ route('admin.invoices.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Invoice #</th>
                            <th class="px-5 py-3">Client</th>
                            <th class="px-5 py-3">Total</th>
                            <th class="px-5 py-3">Due</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @forelse($recentInvoices as $inv)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono font-semibold text-blue-600">
                                    <a href="{{ route('admin.invoices.show', $inv->id) }}" class="hover:underline">{{ $inv->reference_number }}</a>
                                </td>
                                <td class="px-5 py-3 font-semibold text-slate-900">
                                    {{ $inv->client->name ?? 'N/A' }}
                                </td>
                                <td class="px-5 py-3 font-mono font-bold text-slate-900">
                                    ₹{{ number_format((float)$inv->total, 2) }}
                                </td>
                                <td class="px-5 py-3 font-mono font-bold text-amber-600">
                                    ₹{{ number_format((float)$inv->amount_due, 2) }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 text-[11px] font-bold rounded-full {{ $inv->status->badgeClass() }}">
                                        {{ $inv->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-500">
                                    No invoices issued yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments Ledger (Phase 6D) -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Recent Payments Ledger</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Online Razorpay & manual payment entries</p>
                </div>
                <a href="{{ route('admin.payments.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Payment Ref</th>
                            <th class="px-5 py-3">Client</th>
                            <th class="px-5 py-3 text-right">Amount</th>
                            <th class="px-5 py-3">Method</th>
                            <th class="px-5 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @forelse($recentPayments as $pay)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono font-semibold text-blue-600">
                                    <a href="{{ route('admin.payments.show', $pay->id) }}" class="hover:underline">{{ $pay->reference_number }}</a>
                                </td>
                                <td class="px-5 py-3 font-semibold text-slate-900">
                                    {{ $pay->client->name ?? 'N/A' }}
                                </td>
                                <td class="px-5 py-3 text-right font-mono font-bold text-emerald-600">
                                    ₹{{ number_format((float)$pay->amount, 2) }}
                                </td>
                                <td class="px-5 py-3 capitalize text-slate-700">
                                    {{ $pay->payment_method ?? 'Manual' }}
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex px-2 py-0.5 text-[11px] font-bold rounded-full {{ $pay->status->badgeClass() }}">
                                        {{ $pay->status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-xs text-slate-500">
                                    No payments recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Leads & Clients Grid (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Leads Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Recent Leads</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Latest requirement inquiries</p>
                </div>
                <a href="{{ route('admin.leads.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Reference</th>
                            <th class="px-5 py-3">Client / Company</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentLeads as $lead)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3.5 font-mono text-xs font-semibold text-blue-600">
                                    {{ $lead->reference_number }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-slate-900">{{ $lead->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $lead->company_name ?? $lead->email }}</div>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full bg-slate-100 text-slate-800 border border-slate-200">
                                        {{ $lead->status->label() }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('admin.leads.show', $lead->id) }}" class="text-xs font-semibold text-blue-600 hover:underline">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-xs text-slate-500">
                                    No leads recorded yet. <a href="{{ route('admin.leads.create') }}" class="text-blue-600 font-semibold hover:underline">Create a lead</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Clients Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Recent Clients</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Newly registered client accounts</p>
                </div>
                <a href="{{ route('admin.clients.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View All &rarr;</a>
            </div>
            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3">Client</th>
                            <th class="px-5 py-3">Company</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($recentClients as $client)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3.5">
                                    <div class="font-semibold text-slate-900">{{ $client->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $client->email }}</div>
                                </td>
                                <td class="px-5 py-3.5 text-xs text-slate-700">
                                    {{ $client->clientProfile->company_name ?? 'N/A' }}
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex px-2 py-0.5 text-xs font-bold rounded-full {{ $client->status->value === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                        {{ ucfirst($client->status->value) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <a href="{{ route('admin.clients.show', $client->id) }}" class="text-xs font-semibold text-blue-600 hover:underline">Profile</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-xs text-slate-500">
                                    No client accounts found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

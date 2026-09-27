@extends('layouts.admin')

@section('title', 'Create Invoice - Admin CRM')
@section('breadcrumb', 'Create Invoice')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create Commercial Invoice</h1>
            <p class="text-slate-500 text-sm mt-1">Issue a formal invoice for client billing or quotation milestone.</p>
        </div>
        <a href="{{ route('admin.invoices.index') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm px-4 py-2 rounded-xl transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Invoices
        </a>
    </div>

    <!-- Main Form Card -->
    <form action="{{ route('admin.invoices.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Client & Source Context</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Client Selection -->
                <div>
                    <label for="client_id" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Client <span class="text-red-500">*</span></label>
                    <select id="client_id" name="client_id" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                        <option value="">Select Client...</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (old('client_id', $selectedClient?->id) == $client->id) ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('client_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Project (Optional)</label>
                    <select id="project_id" name="project_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                        <option value="">No Associated Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}" {{ (old('project_id', $selectedProject?->id) == $project->id) ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Quotation Link -->
                <div>
                    <label for="quotation_id" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Quotation (Optional)</label>
                    <select id="quotation_id" name="quotation_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                        <option value="">No Linked Quotation</option>
                        @foreach($quotations as $quotation)
                            <option value="{{ $quotation->id }}" data-subtotal="{{ $quotation->subtotal }}" data-discount="{{ $quotation->discount }}" data-tax="{{ $quotation->tax }}" {{ (old('quotation_id', $selectedQuotation?->id) == $quotation->id) ? 'selected' : '' }}>
                                {{ $quotation->reference_number }} - ₹{{ number_format((float)$quotation->total, 2) }}
                            </option>
                        @endforeach
                    </select>
                    @error('quotation_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Milestone Link -->
                <div>
                    <label for="milestone_id" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Milestone (Optional)</label>
                    <select id="milestone_id" name="milestone_id" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                        <option value="">No Linked Milestone</option>
                        @foreach($milestones as $milestone)
                            <option value="{{ $milestone->id }}" data-project-id="{{ $milestone->project_id }}" {{ old('milestone_id') == $milestone->id ? 'selected' : '' }}>
                                {{ $milestone->title }} (₹{{ number_format((float)$milestone->amount, 2) }})
                            </option>
                        @endforeach
                    </select>
                    @error('milestone_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Invoice Header & Dates -->
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 pt-2">Schedule & Status</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="issue_date" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Issue Date <span class="text-red-500">*</span></label>
                    <input type="date" id="issue_date" name="issue_date" value="{{ old('issue_date', now()->toDateString()) }}" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                    @error('issue_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="due_date" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Due Date <span class="text-red-500">*</span></label>
                    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', now()->addDays(14)->toDateString()) }}" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                    @error('due_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Initial Status <span class="text-red-500">*</span></label>
                    <select id="status" name="status" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                        @foreach($statuses as $status)
                            <option value="{{ $status->value }}" {{ old('status', 'issued') === $status->value ? 'selected' : '' }}>
                                {{ $status->label() }}
                            </option>
                        @endforeach
                    </select>
                    @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Financial Calculations -->
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 pt-2">Financial Amounts (INR)</h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="subtotal" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Subtotal (₹) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" min="0" id="subtotal" name="subtotal" value="{{ old('subtotal', $selectedQuotation?->subtotal ?? '0.00') }}" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                    @error('subtotal') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="discount" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Discount (₹)</label>
                    <input type="number" step="0.01" min="0" id="discount" name="discount" value="{{ old('discount', $selectedQuotation?->discount ?? '0.00') }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                    @error('discount') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="tax" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Tax (₹)</label>
                    <input type="number" step="0.01" min="0" id="tax" name="tax" value="{{ old('tax', $selectedQuotation?->tax ?? '0.00') }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">
                    @error('tax') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Financial Live Calculation Preview Box -->
            <div class="bg-slate-900 text-white p-5 rounded-2xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <div>
                    <span class="text-xs uppercase tracking-wider text-slate-400 font-semibold">Calculated Total Payable</span>
                    <p class="text-xs text-slate-400 mt-0.5">Subtotal - Discount + Tax</p>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-extrabold text-emerald-400 tracking-tight" id="calculated-total-display">
                        ₹{{ number_format((float)($selectedQuotation?->total ?? 0), 2) }}
                    </span>
                </div>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider mb-1.5">Notes & Terms (Optional)</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Payment terms, bank details, or delivery notes..." class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900">{{ old('notes', $selectedQuotation?->notes) }}</textarea>
                @error('notes') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.invoices.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium text-sm py-2.5 px-5 rounded-xl transition-all">Cancel</a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm py-2.5 px-6 rounded-xl transition-all shadow-md shadow-blue-500/20">
                Save & Issue Invoice
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const subtotalInput = document.getElementById('subtotal');
        const discountInput = document.getElementById('discount');
        const taxInput = document.getElementById('tax');
        const totalDisplay = document.getElementById('calculated-total-display');
        const quotationSelect = document.getElementById('quotation_id');

        function updateTotals() {
            const subtotal = parseFloat(subtotalInput.value) || 0;
            const discount = parseFloat(discountInput.value) || 0;
            const tax = parseFloat(taxInput.value) || 0;
            const total = Math.max(0, subtotal - discount + tax);

            totalDisplay.textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        subtotalInput.addEventListener('input', updateTotals);
        discountInput.addEventListener('input', updateTotals);
        taxInput.addEventListener('input', updateTotals);

        quotationSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.subtotal) {
                subtotalInput.value = parseFloat(selectedOption.dataset.subtotal).toFixed(2);
                discountInput.value = parseFloat(selectedOption.dataset.discount).toFixed(2);
                taxInput.value = parseFloat(selectedOption.dataset.tax).toFixed(2);
                updateTotals();
            }
        });

        updateTotals();
    });
</script>
@endsection

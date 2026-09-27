@extends('layouts.admin')

@section('title', 'Create Quotation')
@section('breadcrumb', 'Create Quotation')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Create New Quotation</h1>
            <p class="text-sm text-slate-500 mt-1">Generate a commercial estimate with structured line items and server-verified totals.</p>
        </div>
        <a href="{{ route('admin.quotations.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm rounded-xl transition-colors">
            Cancel
        </a>
    </div>

    <form action="{{ route('admin.quotations.store') }}" method="POST" id="quotation-form" class="space-y-6">
        @csrf

        <!-- Quotation Meta Section -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Client & Project Context</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Client Selection -->
                <div>
                    <label for="client_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Client <span class="text-rose-500">*</span>
                    </label>
                    <select name="client_id" id="client_id" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">-- Select Client --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (string) old('client_id', $selectedClientId) === (string) $client->id ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->clientProfile->company_name ?? $client->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Project (Optional)
                    </label>
                    <select name="project_id" id="project_id" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">-- None / Select Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}" {{ (string) old('project_id', $selectedProjectId) === (string) $project->id ? 'selected' : '' }}>
                                {{ $project->title }} ({{ $project->reference_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Lead Selection -->
                <div>
                    <label for="lead_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Associated Lead (Optional)
                    </label>
                    <select name="lead_id" id="lead_id" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">-- None / Select Lead --</option>
                        @foreach($leads as $lead)
                            <option value="{{ $lead->id }}" data-client-id="{{ $lead->client_id }}" {{ (string) old('lead_id', $selectedLeadId) === (string) $lead->id ? 'selected' : '' }}>
                                {{ $lead->name }} ({{ $lead->reference_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-4 border-t border-slate-100">
                <!-- Issue Date -->
                <div>
                    <label for="issue_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Issue Date <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <!-- Valid Until -->
                <div>
                    <label for="valid_until" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Valid Until <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="valid_until" id="valid_until" value="{{ old('valid_until', now()->addDays(15)->format('Y-m-d')) }}" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <!-- Initial Status -->
                <div>
                    <label for="status" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                        Status <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" required class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="draft" {{ old('status', 'draft') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="sent" {{ old('status') === 'sent' ? 'selected' : '' }}>Sent</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Quotation Line Items Section -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900">Line Items</h3>
                <button type="button" id="add-item-btn" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Line Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm" id="items-table">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider text-slate-500">
                            <th class="px-3 py-3 w-48">Service (Optional)</th>
                            <th class="px-3 py-3 min-w-[220px]">Description <span class="text-rose-500">*</span></th>
                            <th class="px-3 py-3 w-24">Qty <span class="text-rose-500">*</span></th>
                            <th class="px-3 py-3 w-32">Unit Price (₹) <span class="text-rose-500">*</span></th>
                            <th class="px-3 py-3 w-28">Discount (₹)</th>
                            <th class="px-3 py-3 w-28">Tax (₹)</th>
                            <th class="px-3 py-3 w-36 text-right">Line Total (₹)</th>
                            <th class="px-3 py-3 w-12 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body" class="divide-y divide-slate-200">
                        <!-- Rows rendered via JavaScript -->
                    </tbody>
                </table>
            </div>

            <!-- Financial Summary Card -->
            <div class="flex justify-end pt-4 border-t border-slate-100">
                <div class="w-full max-w-sm bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2 text-sm">
                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span class="font-mono font-bold" id="summary-subtotal">₹0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Total Discount:</span>
                        <span class="font-mono font-bold text-rose-600" id="summary-discount">-₹0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-600">
                        <span>Total Tax:</span>
                        <span class="font-mono font-bold text-slate-800" id="summary-tax">+₹0.00</span>
                    </div>
                    <div class="flex justify-between text-slate-900 font-extrabold text-base pt-2 border-t border-slate-200">
                        <span>Grand Total:</span>
                        <span class="font-mono text-blue-600" id="summary-grand-total">₹0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Terms & Notes Section -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Notes & Payment Terms</h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Notes / Proposal Details</label>
                    <textarea name="notes" id="notes" rows="4" placeholder="Additional notes or custom instructions for the client..." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('notes') }}</textarea>
                </div>

                <div>
                    <label for="terms" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Payment Terms & Conditions</label>
                    <textarea name="terms" id="terms" rows="4" placeholder="e.g. 50% advance upon agreement, 50% upon final delivery. Quotation valid for 15 days." class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">{{ old('terms', "1. 50% advance payment upon acceptance.\n2. Balance 50% upon project milestone delivery.\n3. Validity: 15 calendar days.") }}</textarea>
                </div>
            </div>
        </div>

        <!-- Form Submit Button -->
        <div class="flex justify-end gap-3">
            <a href="{{ route('admin.quotations.index') }}" class="px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-sm rounded-xl transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm rounded-xl shadow-md transition-colors">
                Save Quotation
            </button>
        </div>
    </form>
</div>

<!-- JavaScript for Dynamic Line Item Table -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const services = @json($services);
        const itemsBody = document.getElementById('items-body');
        const addItemBtn = document.getElementById('add-item-btn');
        let rowIndex = 0;

        function createRow(data = {}) {
            const index = rowIndex++;
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/50 align-top';
            tr.dataset.index = index;

            let serviceOptions = `<option value="">-- Custom Item --</option>`;
            services.forEach(s => {
                const selected = data.service_id == s.id ? 'selected' : '';
                serviceOptions += `<option value="${s.id}" data-name="${s.name}" data-desc="${s.short_description || ''}" ${selected}>${s.name}</option>`;
            });

            tr.innerHTML = `
                <td class="px-3 py-3">
                    <select name="items[${index}][service_id]" class="service-select w-full py-2 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none">
                        ${serviceOptions}
                    </select>
                </td>
                <td class="px-3 py-3">
                    <textarea name="items[${index}][description]" required rows="2" placeholder="Item description or scope..." class="desc-input w-full p-2 bg-slate-50 border border-slate-200 rounded-lg text-xs focus:bg-white focus:outline-none">${data.description || ''}</textarea>
                    <input type="hidden" name="items[${index}][sequence_order]" value="${index + 1}">
                </td>
                <td class="px-3 py-3">
                    <input type="number" step="0.01" min="0.01" name="items[${index}][quantity]" value="${data.quantity || '1.00'}" required class="qty-input w-full py-2 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-bold focus:bg-white focus:outline-none">
                </td>
                <td class="px-3 py-3">
                    <input type="number" step="0.01" min="0" name="items[${index}][unit_price]" value="${data.unit_price || '0.00'}" required class="price-input w-full py-2 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono font-bold focus:bg-white focus:outline-none">
                </td>
                <td class="px-3 py-3">
                    <input type="number" step="0.01" min="0" name="items[${index}][discount]" value="${data.discount || '0.00'}" class="discount-input w-full py-2 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono text-slate-600 focus:bg-white focus:outline-none">
                </td>
                <td class="px-3 py-3">
                    <input type="number" step="0.01" min="0" name="items[${index}][tax]" value="${data.tax || '0.00'}" class="tax-input w-full py-2 px-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-mono text-slate-600 focus:bg-white focus:outline-none">
                </td>
                <td class="px-3 py-3 text-right font-mono font-bold text-slate-900 py-3">
                    <span class="line-total-display">₹0.00</span>
                </td>
                <td class="px-3 py-3 text-center">
                    <button type="button" class="remove-row-btn p-1 text-slate-400 hover:text-rose-600 rounded transition-colors" title="Remove Item">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </td>
            `;

            itemsBody.appendChild(tr);

            // Bind Event Listeners
            const serviceSelect = tr.querySelector('.service-select');
            const descInput = tr.querySelector('.desc-input');
            const qtyInput = tr.querySelector('.qty-input');
            const priceInput = tr.querySelector('.price-input');
            const discountInput = tr.querySelector('.discount-input');
            const taxInput = tr.querySelector('.tax-input');
            const removeBtn = tr.querySelector('.remove-row-btn');

            serviceSelect.addEventListener('change', function() {
                const opt = this.options[this.selectedIndex];
                if (this.value && opt) {
                    if (!descInput.value) {
                        descInput.value = opt.dataset.name + (opt.dataset.desc ? ': ' + opt.dataset.desc : '');
                    }
                }
                recalculate();
            });

            [qtyInput, priceInput, discountInput, taxInput].forEach(inp => {
                inp.addEventListener('input', recalculate);
            });

            removeBtn.addEventListener('click', function() {
                if (itemsBody.children.length > 1) {
                    tr.remove();
                    recalculate();
                } else {
                    alert('A quotation must have at least one line item.');
                }
            });

            recalculate();
        }

        function recalculate() {
            let subtotal = 0;
            let totalDiscount = 0;
            let totalTax = 0;

            const rows = itemsBody.querySelectorAll('tr');
            rows.forEach(tr => {
                const qty = parseFloat(tr.querySelector('.qty-input').value) || 0;
                const price = parseFloat(tr.querySelector('.price-input').value) || 0;
                const discount = parseFloat(tr.querySelector('.discount-input').value) || 0;
                const tax = parseFloat(tr.querySelector('.tax-input').value) || 0;

                const lineSubtotal = qty * price;
                const rawLineTotal = lineSubtotal - discount + tax;
                const lineTotal = Math.max(0, rawLineTotal);

                tr.querySelector('.line-total-display').textContent = '₹' + lineTotal.toFixed(2);

                subtotal += lineSubtotal;
                totalDiscount += discount;
                totalTax += tax;
            });

            const grandTotal = Math.max(0, subtotal - totalDiscount + totalTax);

            document.getElementById('summary-subtotal').textContent = '₹' + subtotal.toFixed(2);
            document.getElementById('summary-discount').textContent = '-₹' + totalDiscount.toFixed(2);
            document.getElementById('summary-tax').textContent = '+₹' + totalTax.toFixed(2);
            document.getElementById('summary-grand-total').textContent = '₹' + grandTotal.toFixed(2);
        }

        addItemBtn.addEventListener('click', () => createRow());

        // Initialize with default row or old input items
        @if(old('items'))
            const oldItems = @json(old('items'));
            Object.values(oldItems).forEach(item => createRow(item));
        @else
            createRow();
        @endif
    });
</script>
@endsection

@extends('layouts.admin')

@section('title', 'Create Lead Proposal')
@section('breadcrumb', 'Leads / Create')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.leads.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-2">
                &larr; Back to Leads Pipeline
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Create Lead Proposal</h1>
            <p class="text-sm text-slate-500">Record a new client project inquiry or inbound lead opportunity.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ route('admin.leads.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <!-- Section 1: Contact Information -->
        <div>
            <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-200 mb-4">Contact & Client Information</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Full Contact Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Rahul Sharma" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Address *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="rahul@example.com" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+91 9876543210" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Company / Organization</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Acme Software Systems" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>
            </div>
        </div>

        <!-- Section 2: Pipeline & Assignment -->
        <div>
            <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-200 mb-4">Pipeline & Assignment</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Pipeline Stage *</label>
                    <select name="status" required class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        @foreach($statuses as $st)
                            <option value="{{ $st->value }}" {{ old('status', 'new') === $st->value ? 'selected' : '' }}>
                                {{ $st->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Lead Source</label>
                    <input type="text" name="source" value="{{ old('source', 'website') }}" placeholder="e.g. Website Form / Referral" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Assign Staff</label>
                    <select name="assigned_user_id" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        <option value="">Unassigned</option>
                        @foreach($staffUsers as $staff)
                            <option value="{{ $staff->id }}" {{ old('assigned_user_id') == $staff->id ? 'selected' : '' }}>
                                {{ $staff->name }} ({{ ucfirst($staff->role->value) }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Link Registered Client Account (Optional)</label>
                <select name="client_id" class="w-full px-3.5 py-2 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    <option value="">No Client Linked (Standalone Lead)</option>
                    @foreach($clients as $c)
                        <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} — {{ $c->email }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Section 3: Project Requirements / Notes -->
        <div>
            <h3 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-200 mb-4">Project Requirements & Scope Notes</h3>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Requirement Notes / Scope Summary</label>
                <textarea name="notes" rows="4" placeholder="Detail client functional requirements, target tech stack, timeline expectations, or budget range..." class="w-full px-3.5 py-2.5 text-sm rounded-lg border border-slate-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">{{ old('notes') }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.leads.index') }}" class="btn-base btn-outline btn-sm">
                Cancel
            </a>
            <button type="submit" class="btn-base btn-primary btn-sm">
                Save & Create Lead
            </button>
        </div>
    </form>
</div>
@endsection

@extends('layouts.partner')

@section('title', 'Submit New Referral')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <a href="{{ route('partner.referrals.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Referrals
        </a>
        <span class="font-mono text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200 px-3 py-1 rounded-full">
            Attributed to: {{ $partner->referral_code }}
        </span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Card Header -->
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-gradient-to-r from-slate-50/80 to-white">
            <div class="flex items-center gap-3 mb-2">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Submit a Direct Referral</h1>
                    <p class="text-xs sm:text-sm text-slate-500">Introduce a prospect business seeking custom software, web applications, or digital solutions.</p>
                </div>
            </div>
        </div>

        <!-- Submission Form -->
        <form method="POST" action="{{ route('partner.referrals.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Section 1: Business & Contact Information -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Prospect Organization & Contact Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Business / Company Name -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label for="company_name" class="block text-xs font-bold text-slate-700">
                            Business / Company Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}" required placeholder="e.g. Acme Retail Solutions / Apex Tech Pvt Ltd" class="w-full bg-slate-50 border @error('company_name') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                        @error('company_name')
                            <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Person Name -->
                    <div class="space-y-1.5">
                        <label for="contact_name" class="block text-xs font-bold text-slate-700">
                            Contact Person Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="contact_name" id="contact_name" value="{{ old('contact_name') }}" required placeholder="e.g. John Doe" class="w-full bg-slate-50 border @error('contact_name') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                        @error('contact_name')
                            <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="space-y-1.5">
                        <label for="email" class="block text-xs font-bold text-slate-700">
                            Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="e.g. john@acme.com" class="w-full bg-slate-50 border @error('email') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                        @error('email')
                            <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone Number -->
                    <div class="space-y-1.5 md:col-span-2">
                        <label for="phone" class="block text-xs font-bold text-slate-700">
                            Phone Number <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required placeholder="e.g. +91 98765 43210 or +1 (555) 234-5678" class="w-full bg-slate-50 border @error('phone') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                        <p class="text-[11px] text-slate-400">Include country code if outside India. Must contain at least 7 digits.</p>
                        @error('phone')
                            <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section 2: Project Scope & Requirements -->
            <div class="pt-4 border-t border-slate-100">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Project Requirements & Scope</h3>

                <div class="space-y-5">
                    <!-- Business Requirement / Project Description -->
                    <div class="space-y-1.5">
                        <label for="requirement" class="block text-xs font-bold text-slate-700">
                            Business Requirement / Project Description <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="requirement" id="requirement" rows="4" required placeholder="Describe what the business is looking to build (e.g. multi-vendor marketplace, hospital booking app, internal inventory CRM, website redesign, etc.)..." class="w-full bg-slate-50 border @error('requirement') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">{{ old('requirement') }}</textarea>
                        @error('requirement')
                            <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Estimated Budget (Optional) -->
                        <div class="space-y-1.5">
                            <label for="estimated_budget" class="block text-xs font-bold text-slate-700">
                                Estimated Budget <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="estimated_budget" id="estimated_budget" value="{{ old('estimated_budget') }}" placeholder="e.g. ₹50,000 - ₹1,50,000 or $2,000 - $5,000" class="w-full bg-slate-50 border @error('estimated_budget') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                            @error('estimated_budget')
                                <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Additional Notes (Optional) -->
                        <div class="space-y-1.5">
                            <label for="notes" class="block text-xs font-bold text-slate-700">
                                Additional Notes <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input type="text" name="notes" id="notes" value="{{ old('notes') }}" placeholder="e.g. Preferred call timing, key decision-maker role" class="w-full bg-slate-50 border @error('notes') border-rose-300 ring-1 ring-rose-300 @else border-slate-200 @enderror rounded-xl px-4 py-2.5 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                            @error('notes')
                                <p class="text-xs text-rose-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Anti-Fraud & Attribution Notice -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-500 space-y-1">
                <div class="font-bold text-slate-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Referral Attribution & Anti-Fraud Protection
                </div>
                <p>Referrals must be genuine, independent third-party businesses. Self-referrals are strictly prohibited. Submitted referrals will be verified and tracked under code <strong class="text-slate-800 font-mono">{{ $partner->referral_code }}</strong>.</p>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('partner.referrals.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Submit Referral
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

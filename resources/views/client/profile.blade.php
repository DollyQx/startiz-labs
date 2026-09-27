@extends('layouts.client')

@section('title', 'My Profile')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    <!-- Page Header -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Client Account Profile</h1>
        <p class="text-sm text-slate-500 mt-1">Manage your account credentials, business contact info, and billing addresses.</p>
    </div>

    <!-- Profile Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="POST" action="{{ route('client.profile.update') }}" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- Account Details Section -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-blue-600 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Account & Personal Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Full Name *</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                        @error('name')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1.5">Email Address (Read-only)</label>
                        <input type="email" id="email" value="{{ $user->email }}" disabled class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 bg-slate-100 text-slate-500 font-mono cursor-not-allowed">
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Phone Number</label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone ?? $profile->phone) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                        @error('phone')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Business & Company Details Section -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-indigo-600 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Company & Business Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="company_name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Company / Organization Name</label>
                        <input type="text" id="company_name" name="company_name" value="{{ old('company_name', $profile->company_name) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                    </div>

                    <div>
                        <label for="contact_person" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Contact Person</label>
                        <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person', $profile->contact_person) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                    </div>

                    <div>
                        <label for="gst_vat_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">GST / VAT Number</label>
                        <input type="text" id="gst_vat_number" name="gst_vat_number" value="{{ old('gst_vat_number', $profile->gst_vat_number) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium font-mono">
                    </div>

                    <div>
                        <label for="industry" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Industry / Sector</label>
                        <input type="text" id="industry" name="industry" value="{{ old('industry', $profile->industry) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                    </div>

                    <div class="sm:col-span-2">
                        <label for="website" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Website URL</label>
                        <input type="url" id="website" name="website" value="{{ old('website', $profile->website) }}" placeholder="https://example.com" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                        @error('website')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Billing Address Section -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-emerald-600 border-b border-slate-100 pb-2 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Billing & Address Details
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label for="address_line1" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Address Line 1</label>
                        <input type="text" id="address_line1" name="address_line1" value="{{ old('address_line1', $profile->address_line1) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">City</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $profile->city) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                    </div>

                    <div>
                        <label for="state" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">State / Province</label>
                        <input type="text" id="state" name="state" value="{{ old('state', $profile->state) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                    </div>

                    <div>
                        <label for="postal_code" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Postal Code</label>
                        <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $profile->postal_code) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium font-mono">
                    </div>

                    <div>
                        <label for="country" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Country</label>
                        <input type="text" id="country" name="country" value="{{ old('country', $profile->country) }}" class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 text-slate-900 font-medium">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm px-6 py-3 rounded-xl transition-all shadow-md shadow-blue-500/20">
                    Save Profile Changes
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

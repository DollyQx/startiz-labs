@extends('layouts.partner')

@section('title', 'Payout Preferences & Profile')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Partner Settings & Payout Preferences</h1>
        <p class="text-sm text-slate-500">Manage your contact information and banking/UPI settlement details</p>
    </div>

    <!-- Read-Only Program Tier Card -->
    <div class="bg-gradient-to-r from-slate-900 to-slate-800 rounded-2xl p-6 text-white shadow-md">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Assigned Referral Code</span>
                <div class="font-mono text-xl font-black text-emerald-400 mt-1 select-all">{{ $partner->referral_code }}</div>
            </div>
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Commission Rate</span>
                <div class="font-mono text-xl font-black text-white mt-1">{{ $partner->commission_rate }}%</div>
            </div>
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Account Status</span>
                <div class="mt-1">
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        {{ $partner->status->label() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <form method="POST" action="{{ route('partner.profile.update') }}" class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <h3 class="font-bold text-slate-900 text-lg border-b border-slate-100 pb-3">Contact Information</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Full Name *</label>
                <input type="text" name="name" id="name" required value="{{ old('name', $user->name) }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1">Email Address (Locked)</label>
                <input type="email" id="email" disabled value="{{ $user->email }}" class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-500 cursor-not-allowed">
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Phone / WhatsApp Number</label>
                <input type="text" name="phone" id="phone" value="{{ old('phone', $partner->phone ?? $user->phone) }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="+91 9876543210">
            </div>

            <div>
                <label for="company_name" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Company / Agency Name</label>
                <input type="text" name="company_name" id="company_name" value="{{ old('company_name', $partner->company_name) }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="Acme Media LLC">
            </div>

            <div class="sm:col-span-2">
                <label for="website" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Website URL (Optional)</label>
                <input type="url" name="website" id="website" value="{{ old('website', $partner->website) }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" placeholder="https://example.com">
            </div>
        </div>

        <h3 class="font-bold text-slate-900 text-lg border-b border-slate-100 pb-3 pt-4">Payout Settlement Details</h3>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">Preferred Payout Method *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50">
                        <input type="radio" name="payout_method" value="upi" {{ old('payout_method', $partner->payout_method) === 'upi' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">UPI (India)</span>
                            <span class="text-xs text-slate-500">Google Pay, PhonePe, Paytm (e.g. name@okaxis)</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50">
                        <input type="radio" name="payout_method" value="bank_transfer" {{ old('payout_method', $partner->payout_method) === 'bank_transfer' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <span class="block text-sm font-bold text-slate-900">Direct Bank Wire / NEFT</span>
                            <span class="text-xs text-slate-500">Account Number, Bank Name, IFSC / SWIFT</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label for="payout_details" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                    Payout Destination Details *
                </label>
                <textarea name="payout_details" id="payout_details" rows="3" required class="w-full bg-slate-50 border border-slate-300 rounded-xl p-4 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 font-mono" placeholder="If UPI: enter VPA (alex@upi). If Bank: enter Account Holder, Account #, Bank Name, and IFSC Code.">{{ old('payout_details', $partner->payout_details) }}</textarea>
                <p class="text-xs text-slate-400 mt-1">These details are kept strictly private and used exclusively by finance administrators to disburse approved commissions.</p>
            </div>
        </div>

        <div class="pt-4 flex justify-end">
            <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm">
                Save Preferences
            </button>
        </div>
    </form>
</div>
@endsection

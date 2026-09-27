@extends('layouts.admin')

@section('title', 'Client Profile: ' . $client->name)
@section('breadcrumb', 'Clients / Details')

@section('content')
<div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.clients.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-2">
                &larr; Back to Clients Directory
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">{{ $client->name }}</h1>
            <p class="text-sm text-slate-500">{{ $client->clientProfile->company_name ?? 'Individual Client Account' }}</p>
        </div>
        <div>
            <span class="inline-flex px-3 py-1 text-xs font-extrabold rounded-full {{ $client->status->value === 'active' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                Account Status: {{ strtoupper($client->status->value) }}
            </span>
        </div>
    </div>

    <!-- Quick Metric Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase text-slate-400 block">Related Projects</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $client->projects_count }}</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase text-slate-400 block">Requirement Leads</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $client->leads_count }}</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase text-slate-400 block">Issued Quotations</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $client->quotations_count }}</span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <span class="text-xs font-bold uppercase text-slate-400 block">Invoices Generated</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ $client->invoices_count }}</span>
        </div>
    </div>

    <!-- Main Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Account Details -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Account Information
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Full Name</span>
                    <span class="font-medium text-slate-900">{{ $client->name }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Email Address</span>
                    <span class="font-medium text-slate-900">{{ $client->email }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Phone Number</span>
                    <span class="font-medium text-slate-900">{{ $client->phone ?? $client->clientProfile->phone ?? 'Not provided' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Email Verification</span>
                    <span class="font-medium {{ $client->email_verified_at ? 'text-emerald-600' : 'text-amber-600' }}">
                        {{ $client->email_verified_at ? 'Verified (' . $client->email_verified_at->format('M d, Y') . ')' : 'Pending Verification' }}
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Joined Date</span>
                    <span class="font-medium text-slate-900">{{ $client->created_at->format('M d, Y H:i A') }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Last Login</span>
                    <span class="font-medium text-slate-900">{{ $client->last_login_at ? $client->last_login_at->format('M d, Y H:i A') : 'Never' }}</span>
                </div>
            </div>
        </div>

        <!-- Company / Business Details -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V9m0 0h5m-5 0H7"/></svg>
                Company Profile
            </h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Company Name</span>
                    <span class="font-medium text-slate-900">{{ $client->clientProfile->company_name ?? 'Individual / Sole Proprietor' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Industry Sector</span>
                    <span class="font-medium text-slate-900">{{ $client->clientProfile->industry ?? 'General' }}</span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Company Website</span>
                    <span class="font-medium text-slate-900">
                        @if(!empty($client->clientProfile->website))
                            <a href="{{ $client->clientProfile->website }}" target="_blank" class="text-blue-600 hover:underline">{{ $client->clientProfile->website }}</a>
                        @else
                            Not provided
                        @endif
                    </span>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold uppercase block">Tax / GST Number</span>
                    <span class="font-medium text-slate-900">{{ $client->clientProfile->tax_id ?? $client->clientProfile->gst_vat_number ?? 'Not provided' }}</span>
                </div>
            </div>

            <div class="pt-2">
                <span class="text-xs text-slate-500 font-semibold uppercase block mb-1">Billing Address</span>
                <p class="text-sm text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-200">
                    @if($client->clientProfile && ($client->clientProfile->address_line1 || $client->clientProfile->city))
                        {{ implode(', ', array_filter([$client->clientProfile->address_line1, $client->clientProfile->city, $client->clientProfile->state, $client->clientProfile->country])) }}
                    @else
                        No address registered
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

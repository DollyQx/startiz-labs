@extends('layouts.partner')

@section('title', 'Partner Resources & Marketing Kit')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    <!-- Header with Submit a Referral CTA -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Partner Resources & Outreach Kit</h1>
            <p class="text-sm text-slate-500 mt-1">Tools, pre-written templates, and service guides to help you introduce prospective clients to Startiz Labs.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('partner.referrals.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition-all">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Submit a Referral
            </a>
        </div>
    </div>

    <!-- Section D: Referral Link Card -->
    <div class="bg-gradient-to-br from-white to-emerald-50/40 rounded-2xl border border-emerald-100 p-6 sm:p-8 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Your Active Tracking URL</span>
            </div>
            <span class="font-mono text-xs font-bold bg-white text-emerald-800 border border-emerald-200 px-3 py-1 rounded-full shadow-2xs">
                Partner Code: {{ $partner->referral_code }}
            </span>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <input type="text" id="primary-ref-link" readonly value="{{ $referralUrl }}" class="flex-1 bg-white border border-slate-200 text-slate-800 font-mono text-sm rounded-xl px-4 py-3 select-all focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-2xs">
            <button onclick="copyToClipboard('primary-ref-link', 'Referral URL copied to clipboard!')" class="shrink-0 px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                Copy Link
            </button>
            @php
                $waTextPrimary = urlencode("Need custom software, web applications, or digital solutions for your business? Check out Startiz Labs: " . $referralUrl);
            @endphp
            <a href="https://api.whatsapp.com/send?text={{ $waTextPrimary }}" target="_blank" rel="noopener noreferrer" class="shrink-0 px-6 py-3 bg-[#25D366] hover:bg-[#20ba59] text-white font-bold text-sm rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                Share on WhatsApp
            </a>
        </div>
        <p class="text-xs text-slate-500 mt-3">
            Visitors attributed via this link have their session & 60-day cookie marked with your referral code.
        </p>
    </div>

    <!-- Section A: Partner Introduction -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="space-y-3">
                <h2 class="text-lg font-bold text-slate-900">How to Approach Businesses &amp; Leads</h2>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Most growing businesses struggle with disconnected spreadsheets, outdated legacy websites, or off-the-shelf software that cannot scale with their operations. As a Startiz Labs Partner, your role is to identify these friction points and introduce them to our engineering team.
                </p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-2">
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-700">1. Spot the Opportunity</span>
                        <p class="text-xs text-slate-600 mt-1">Look for businesses with slow websites, lack of mobile presence, or manual back-office tasks that need workflow automation.</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-700">2. Introduce the Solution</span>
                        <p class="text-xs text-slate-600 mt-1">Share your unique referral link or forward one of our ready-to-use messages explaining Startiz Labs' engineering capabilities.</p>
                    </div>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-700">3. Submit Direct Referrals</span>
                        <p class="text-xs text-slate-600 mt-1">For direct conversations or high-intent warm leads, submit their details directly via our manual referral submission form.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section B: READY-TO-USE MESSAGES -->
    <div class="space-y-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Ready-to-Use Outreach Messages</h2>
            <p class="text-xs text-slate-500">Copy pre-composed messages tailored for WhatsApp, Telegram, and LinkedIn, each automatically embedded with your referral link.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- WhatsApp Message Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                            WhatsApp
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">Direct & Quick</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 text-xs text-slate-700 leading-relaxed font-sans select-all whitespace-pre-wrap" id="msg-whatsapp">{{ $messages['whatsapp'] }}</div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2">
                    <button onclick="copyToClipboard('msg-whatsapp', 'WhatsApp message copied!')" class="flex-1 py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy Message
                    </button>
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($messages['whatsapp']) }}" target="_blank" rel="noopener noreferrer" class="py-2 px-3 bg-[#25D366] hover:bg-[#20ba59] text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1">
                        Send
                    </a>
                </div>
            </div>

            <!-- Telegram Message Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.894 8.221l-1.97 9.28c-.145.658-.537.818-1.084.508l-3-2.21-1.446 1.394c-.16.16-.295.295-.605.295l.213-3.053 5.56-5.023c.242-.213-.054-.333-.373-.121l-6.871 4.326-2.962-.924c-.643-.204-.657-.643.136-.953l11.57-4.458c.535-.194 1.006.128.832.939z"/></svg>
                            Telegram
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">Conversational</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 text-xs text-slate-700 leading-relaxed font-sans select-all whitespace-pre-wrap" id="msg-telegram">{{ $messages['telegram'] }}</div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2">
                    <button onclick="copyToClipboard('msg-telegram', 'Telegram message copied!')" class="flex-1 py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy Message
                    </button>
                    <a href="https://t.me/share/url?url={{ urlencode($referralUrl) }}&text={{ urlencode($messages['telegram']) }}" target="_blank" rel="noopener noreferrer" class="py-2 px-3 bg-[#0088cc] hover:bg-[#0077b3] text-white rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1">
                        Send
                    </a>
                </div>
            </div>

            <!-- LinkedIn Message Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            LinkedIn
                        </span>
                        <span class="text-[11px] text-slate-400 font-medium">B2B Professional</span>
                    </div>
                    <div class="bg-slate-50 border border-slate-100 rounded-xl p-3.5 text-xs text-slate-700 leading-relaxed font-sans select-all whitespace-pre-wrap" id="msg-linkedin">{{ $messages['linkedin'] }}</div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 flex items-center gap-2">
                    <button onclick="copyToClipboard('msg-linkedin', 'LinkedIn message copied!')" class="w-full py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-bold transition-colors flex items-center justify-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Copy Message
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Section C: SERVICES TO REFER -->
    <div class="space-y-4">
        <div>
            <h2 class="text-lg font-bold text-slate-900 tracking-tight">Services You Can Refer</h2>
            <p class="text-xs text-slate-500">Official engineering services offered by Startiz Labs. Highlight these when discussing project requirements with leads.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($services as $service)
                <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:border-emerald-300 transition-colors flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2">
                            <h3 class="font-bold text-slate-900 text-sm">{{ $service['name'] }}</h3>
                            <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">
                                {{ $service['tag'] }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 leading-relaxed">{{ $service['description'] }}</p>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-emerald-700 font-semibold font-mono text-[11px]">20% Commission Eligible</span>
                        <a href="{{ route('partner.referrals.create') }}" class="text-slate-900 hover:text-emerald-600 font-bold">Refer &rarr;</a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Section E: Prominent SUBMIT A REFERRAL CTA Card -->
    <div class="bg-slate-900 text-white rounded-2xl p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 shadow-lg">
        <div class="space-y-2 max-w-2xl">
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Direct Client Introductions</span>
            <h2 class="text-xl sm:text-2xl font-black tracking-tight">Have a warm business lead ready to discuss their project?</h2>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Submit their project details directly. Our enterprise consulting and delivery team will review the requirements, conduct discovery, and keep you updated on progress.
            </p>
        </div>
        <div class="shrink-0">
            <a href="{{ route('partner.referrals.create') }}" class="inline-flex items-center gap-2 px-6 py-3.5 bg-emerald-500 hover:bg-emerald-400 text-slate-950 rounded-xl text-sm font-extrabold shadow-md transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Submit a Referral
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyToClipboard(elementId, alertMsg) {
        const el = document.getElementById(elementId);
        if (!el) return;
        const text = el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' ? el.value : el.innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert(alertMsg);
        }).catch(() => {
            // Fallback for older browsers
            const temp = document.createElement('textarea');
            temp.value = text;
            document.body.appendChild(temp);
            temp.select();
            document.execCommand('copy');
            document.body.removeChild(temp);
            alert(alertMsg);
        });
    }
</script>
@endpush
@endsection

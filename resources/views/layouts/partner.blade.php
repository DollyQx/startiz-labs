<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Partner Workspace') — Startiz Labs Partner Portal</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#f0fdf4',
                            100: '#dcfce7',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col min-h-screen">

    <!-- Partner Header Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('partner.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-600 flex items-center justify-center text-white font-extrabold shadow-md shadow-emerald-500/20">
                            STZ
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-900 tracking-tight text-lg">STARTIZ LABS</span>
                            <span class="block text-[10px] font-bold text-emerald-600 uppercase tracking-widest leading-none">Partner Portal</span>
                        </div>
                    </a>

                    <!-- Desktop Primary Navigation -->
                    <nav class="hidden lg:flex items-center gap-1">
                        <a href="{{ route('partner.dashboard') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('partner.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('partner.referrals.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('partner.referrals.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Referrals & Leads
                        </a>
                        <a href="{{ route('partner.resources') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('partner.resources') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Resources
                        </a>
                        <a href="{{ route('partner.earnings.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('partner.earnings.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Commissions & Payouts
                        </a>
                        <a href="{{ route('partner.leaderboard') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('partner.leaderboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Leaderboard
                        </a>
                        <a href="{{ route('partner.links') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('partner.links') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Referral Links
                        </a>
                        <a href="{{ route('partner.profile') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('partner.profile') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Payout Preferences
                        </a>
                    </nav>
                </div>

                <!-- Right Header Elements -->
                <div class="flex items-center gap-3">
                    @if(auth()->user()?->partnerProfile)
                        <!-- Referral Code Pill -->
                        <div class="hidden sm:flex items-center gap-2 bg-emerald-50 border border-emerald-200/80 rounded-lg px-3 py-1.5">
                            <span class="text-xs font-medium text-emerald-700">Code:</span>
                            <span class="font-mono text-xs font-bold text-emerald-800 tracking-wider select-all">{{ auth()->user()->partnerProfile->referral_code }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ auth()->user()->partnerProfile->referralUrl() }}'); alert('Referral URL copied to clipboard!');" class="text-emerald-600 hover:text-emerald-800 ml-1" title="Copy Referral Link">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    @endif

                    <!-- User Profile & Logout -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                        <div class="text-right hidden md:block">
                            <span class="block text-xs font-bold text-slate-800 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="block text-[11px] font-medium text-emerald-600">{{ auth()->user()->partnerProfile?->company_name ?? 'Partner Account' }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Sign Out">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    </div>

                    <!-- Mobile Menu Button -->
                    <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="lg:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-lg">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-1">
            <a href="{{ route('partner.dashboard') }}" class="block px-3 py-2 text-base font-semibold rounded-lg {{ request()->routeIs('partner.dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }}">Dashboard</a>
            <a href="{{ route('partner.referrals.index') }}" class="block px-3 py-2 text-base font-semibold rounded-lg {{ request()->routeIs('partner.referrals.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }}">Referrals & Leads</a>
            <a href="{{ route('partner.resources') }}" class="block px-3 py-2 text-base font-semibold rounded-lg {{ request()->routeIs('partner.resources') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }}">Resources</a>
            <a href="{{ route('partner.earnings.index') }}" class="block px-3 py-2 text-base font-semibold rounded-lg {{ request()->routeIs('partner.earnings.*') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }}">Commissions & Payouts</a>
            <a href="{{ route('partner.leaderboard') }}" class="block px-3 py-2 text-base font-semibold rounded-lg {{ request()->routeIs('partner.leaderboard') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }}">Leaderboard</a>
            <a href="{{ route('partner.links') }}" class="block px-3 py-2 text-base font-semibold rounded-lg {{ request()->routeIs('partner.links') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }}">Referral Links</a>
            <a href="{{ route('partner.profile') }}" class="block px-3 py-2 text-base font-semibold rounded-lg {{ request()->routeIs('partner.profile') ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600' }}">Payout Preferences</a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Global Flash Alerts -->
            @if(session('status'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-semibold">{{ session('status') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 flex items-center gap-3 text-rose-800">
                    <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="text-sm font-semibold">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800">
                    <div class="flex items-center gap-2 mb-2 font-bold text-sm">
                        <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>Please correct the errors below:</span>
                    </div>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>&copy; {{ date('Y') }} Startiz Labs. Official Partner Workspace. All rights reserved.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="hover:text-slate-800">Main Website</a>
                <a href="{{ route('contact') }}" class="hover:text-slate-800">Partner Support</a>
                <a href="{{ route('services.index') }}" class="hover:text-slate-800">Services Portfolio</a>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>

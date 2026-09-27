<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Client Workspace') — Startiz Labs</title>
    
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
                            50: '#f0f7ff',
                            100: '#e0effe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 flex flex-col min-h-screen">

    <!-- Client Header Navigation -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('client.dashboard') }}" class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-extrabold shadow-md shadow-blue-500/20">
                            STZ
                        </div>
                        <div>
                            <span class="font-extrabold text-slate-900 tracking-tight text-lg">STARTIZ LABS</span>
                            <span class="block text-[10px] font-bold text-blue-600 uppercase tracking-widest leading-none">Client Portal</span>
                        </div>
                    </a>

                    <!-- Desktop Primary Navigation -->
                    <nav class="hidden md:flex items-center gap-1">
                        <a href="{{ route('client.dashboard') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('client.projects.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.projects.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Projects
                        </a>
                        <a href="{{ route('client.quotations.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.quotations.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Quotations
                        </a>
                        <a href="{{ route('client.invoices.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.invoices.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Invoices
                        </a>
                        <a href="{{ route('client.payments.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.payments.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Payments
                        </a>
                        <a href="{{ route('client.tickets.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.tickets.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Support Tickets
                        </a>
                        <a href="{{ route('client.change-requests.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.change-requests.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Change Requests
                        </a>
                        <a href="{{ route('client.documents.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.documents.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Documents
                        </a>
                        <a href="{{ route('client.activity.index') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.activity.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            Activity History
                        </a>
                        <a href="{{ route('client.profile') }}" class="px-3.5 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('client.profile') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-100' }}">
                            My Profile
                        </a>
                    </nav>
                </div>

                <!-- Right Side Actions & User Profile -->
                <div class="hidden md:flex items-center gap-3">
                    <!-- Notification Bell -->
                    <a href="{{ route('client.notifications.index') }}" class="relative p-2 text-slate-600 hover:text-slate-900 rounded-lg border border-slate-200 bg-slate-50 hover:bg-white transition-colors block" title="Notifications">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-blue-600 text-white text-[10px] font-extrabold flex items-center justify-center shadow-sm">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>

                    <!-- Client Account Info -->
                    <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                        <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm border border-blue-200">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="text-left">
                            <span class="block text-xs font-bold text-slate-900 leading-tight">{{ auth()->user()->name }}</span>
                            <span class="block text-[10px] font-medium text-slate-500 truncate max-w-[140px]">{{ auth()->user()->email }}</span>
                        </div>
                    </div>

                    <!-- Logout Button -->
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-rose-600 bg-slate-100 hover:bg-rose-50 px-3 py-1.5 rounded-lg border border-slate-200 hover:border-rose-200 transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            Logout
                        </button>
                    </form>
                </div>

                <!-- Mobile Menu Toggle -->
                <div class="flex items-center md:hidden">
                    <button id="client-mobile-toggle" type="button" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation Menu -->
        <div id="client-mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white px-4 pt-2 pb-4 space-y-2">
            <a href="{{ route('client.dashboard') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Dashboard</a>
            <a href="{{ route('client.projects.index') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.projects.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Projects</a>
            <a href="{{ route('client.quotations.index') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.quotations.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Quotations</a>
            <a href="{{ route('client.invoices.index') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.invoices.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Invoices</a>
            <a href="{{ route('client.payments.index') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.payments.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Payments</a>
            <a href="{{ route('client.tickets.index') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.tickets.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Support Tickets</a>
            <a href="{{ route('client.change-requests.index') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.change-requests.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Change Requests</a>
            <a href="{{ route('client.documents.index') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.documents.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Documents</a>
            <a href="{{ route('client.notifications.index') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.notifications.*') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">Notifications</a>
            <a href="{{ route('client.profile') }}" class="block px-3 py-2 text-sm font-semibold rounded-lg {{ request()->routeIs('client.profile') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:bg-slate-100' }}">My Profile</a>
            <div class="pt-3 border-t border-slate-200 flex items-center justify-between">
                <div>
                    <span class="block text-xs font-bold text-slate-900">{{ auth()->user()->name }}</span>
                    <span class="block text-[11px] text-slate-500">{{ auth()->user()->email }}</span>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-semibold text-rose-600 bg-rose-50 px-3 py-1.5 rounded-lg border border-rose-200">Logout</button>
                </form>
            </div>
        </div>
    </header>

    <!-- Global Alert Notifications -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
        @if (session('status'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Workspace Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-2">
            <div>
                &copy; {{ date('Y') }} Startiz Labs. All rights reserved. Client Workspace Portal.
            </div>
            <div class="flex items-center gap-4 text-slate-400">
                <span>Secure Client Portal</span>
                <span>•</span>
                <span>Active Account</span>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('client-mobile-toggle');
            const menu = document.getElementById('client-mobile-menu');
            if (toggleBtn && menu) {
                toggleBtn.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>

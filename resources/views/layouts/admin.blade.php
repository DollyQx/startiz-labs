<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Portal') — Startiz Labs CRM</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    @if(file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                theme: {
                    extend: {
                        colors: {
                            slate: {
                                950: '#020617',
                            }
                        }
                    }
                }
            }
        </script>
    @endif
</head>
<body class="h-full font-sans antialiased text-slate-900 selection:bg-blue-600 selection:text-white">
    <div class="min-h-full flex flex-col lg:flex-row">
        
        <!-- Sidebar Navigation (Desktop) -->
        <aside class="hidden lg:flex lg:flex-col lg:w-64 lg:fixed lg:inset-y-0 bg-slate-900 border-r border-slate-800 text-slate-300 z-30">
            <!-- Brand Logo -->
            <div class="h-16 flex items-center px-6 border-b border-slate-800 bg-slate-950">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 font-bold text-lg text-white">
                    <div class="w-8 h-8 rounded bg-blue-600 text-white font-black flex items-center justify-center text-xs">
                        STZ
                    </div>
                    <span class="tracking-wide">Startiz CRM</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-6 text-sm">
                <!-- Overview -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 px-3">Overview</h4>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Dashboard
                    </a>
                </div>

                <!-- CRM -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 px-3">CRM & Accounts</h4>
                    <a href="{{ route('admin.leads.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.leads.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Leads Pipeline
                    </a>
                    <a href="{{ route('admin.clients.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.clients.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V9m0 0h5m-5 0H7"/></svg>
                        Clients Directory
                    </a>
                </div>

                <!-- Delivery -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 px-3">Delivery</h4>
                    <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.projects.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Projects
                    </a>
                    <a href="{{ route('admin.change-requests.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.change-requests.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Change Requests
                    </a>
                    <a href="{{ route('admin.documents.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.documents.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Documents
                    </a>
                </div>

                <!-- Commercial -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 px-3">Commercial</h4>
                    <a href="{{ route('admin.quotations.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.quotations.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        Quotations
                    </a>
                    <a href="{{ route('admin.invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.invoices.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Invoices
                    </a>
                    <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.payments.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Payments & Ledger
                    </a>
                </div>

                <!-- Support -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 mb-2 px-3">Support & System</h4>
                    <a href="{{ route('admin.tickets.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors {{ request()->routeIs('admin.tickets.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Support Tickets
                    </a>
                    <a href="{{ route('admin.notifications.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg transition-colors mt-1 {{ request()->routeIs('admin.notifications.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            Notifications
                        </div>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-500 text-white">
                                {{ auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>
                    <a href="{{ route('admin.activity.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg transition-colors mt-1 {{ request()->routeIs('admin.activity.*') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 text-slate-300' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Activity Audit Logs
                    </a>
                </div>
            </nav>

            <!-- Bottom User Profile Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950 flex items-center justify-between">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="w-9 h-9 rounded-full bg-slate-700 text-white flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="truncate text-xs">
                        <p class="font-bold text-white truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                        <p class="text-slate-400 capitalize truncate">{{ auth()->user()->role->value ?? 'Admin' }}</p>
                    </div>
                </div>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="lg:pl-64 flex flex-col flex-1 min-h-screen">
            <!-- Top Header Bar -->
            <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 sticky top-0 z-20 shadow-sm">
                <!-- Mobile Toggle & Breadcrumbs -->
                <div class="flex items-center gap-4">
                    <button type="button" id="admin-mobile-toggle" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg border border-slate-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <nav class="hidden sm:flex items-center gap-2 text-xs font-medium text-slate-500">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-slate-900">Admin</a>
                        <span>/</span>
                        <span class="text-slate-900 font-semibold">@yield('breadcrumb', 'Dashboard')</span>
                    </nav>
                </div>

                <!-- Right Header Actions -->
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.notifications.index') }}" class="relative p-2 text-slate-600 hover:text-slate-900 rounded-lg border border-slate-200 bg-slate-50 hover:bg-white transition-colors block" title="Notifications">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        @if(auth()->check() && auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-blue-600 text-white text-[10px] font-extrabold flex items-center justify-center shadow-sm">
                                {{ auth()->user()->unreadNotifications->count() > 9 ? '9+' : auth()->user()->unreadNotifications->count() }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('home') }}" target="_blank" class="text-xs font-semibold text-slate-600 hover:text-blue-600 flex items-center gap-1.5 border border-slate-200 px-3 py-1.5 rounded-lg bg-slate-50 hover:bg-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Public Site
                    </a>
                </div>
            </header>

            <!-- Mobile Drawer -->
            <div id="admin-mobile-menu" class="hidden lg:hidden bg-slate-900 text-slate-300 p-4 border-b border-slate-800 space-y-4">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md font-medium text-sm text-slate-200 hover:bg-slate-800">Dashboard</a>
                <a href="{{ route('admin.leads.index') }}" class="block px-3 py-2 rounded-md font-medium text-sm text-slate-200 hover:bg-slate-800">Leads Pipeline</a>
                <a href="{{ route('admin.clients.index') }}" class="block px-3 py-2 rounded-md font-medium text-sm text-slate-200 hover:bg-slate-800">Clients Directory</a>
                <a href="{{ route('admin.projects.index') }}" class="block px-3 py-2 rounded-md font-medium text-sm text-slate-200 hover:bg-slate-800">Projects Directory</a>
                <a href="{{ route('admin.quotations.index') }}" class="block px-3 py-2 rounded-md font-medium text-sm text-slate-200 hover:bg-slate-800">Quotations Directory</a>
                <a href="{{ route('admin.invoices.index') }}" class="block px-3 py-2 rounded-md font-medium text-sm text-slate-200 hover:bg-slate-800">Invoices & Payments</a>
                <form action="{{ route('admin.logout') }}" method="POST" class="pt-2 border-t border-slate-800">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-400 font-semibold hover:bg-slate-800 rounded-md">Logout</button>
                </form>
            </div>

            <!-- Main Body Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm font-medium flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                        <div class="font-bold mb-1">Please correct the following validation errors:</div>
                        <ul class="list-disc list-inside space-y-1 text-xs">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Drawer JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const btn = document.getElementById('admin-mobile-toggle');
            const menu = document.getElementById('admin-mobile-menu');
            if (btn && menu) {
                btn.addEventListener('click', function() {
                    menu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>

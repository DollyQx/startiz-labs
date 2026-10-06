<header class="sticky top-0 z-40 bg-slate-900/95 backdrop-blur border-b border-slate-800 text-white">
    <div class="container-custom">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 text-white font-extrabold text-xl tracking-wider hover:opacity-90 transition-opacity">
                <div class="w-9 h-9 rounded-lg bg-blue-600 flex items-center justify-center text-white font-black text-xs shadow-md shadow-blue-500/20">
                    STZ
                </div>
                <span>STARTIZ LABS</span>
            </a>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-300" aria-label="Main Navigation">
                <a href="{{ route('home') }}" class="hover:text-white transition-colors {{ request()->routeIs('home') ? 'text-blue-400 font-bold' : '' }}">Home</a>
                <a href="{{ route('services.index') }}" class="hover:text-white transition-colors {{ request()->routeIs('services.*') ? 'text-blue-400 font-bold' : '' }}">Services</a>
                <a href="{{ route('industries.index') }}" class="hover:text-white transition-colors {{ request()->routeIs('industries.*') ? 'text-blue-400 font-bold' : '' }}">Industries</a>
                <a href="{{ route('portfolio.index') }}" class="hover:text-white transition-colors {{ request()->routeIs('portfolio.*') ? 'text-blue-400 font-bold' : '' }}">Portfolio</a>
                <a href="{{ route('about') }}" class="hover:text-white transition-colors {{ request()->routeIs('about') ? 'text-blue-400 font-bold' : '' }}">About</a>
                <a href="{{ route('contact') }}" class="hover:text-white transition-colors {{ request()->routeIs('contact') ? 'text-blue-400 font-bold' : '' }}">Contact</a>
                <a href="{{ route('partners') }}" class="hover:text-amber-400 transition-colors {{ request()->routeIs('partners*') ? 'text-amber-400 font-bold' : 'text-amber-300' }} font-bold flex items-center gap-1">
                    <span>Partner</span>
                    <span class="px-1.5 py-0.5 text-[10px] uppercase font-extrabold bg-amber-500/20 text-amber-300 border border-amber-500/30 rounded">20%</span>
                </a>
            </nav>

            <!-- Desktop Action CTAs -->
            <div class="hidden lg:flex items-center gap-4">
                <a href="{{ route('login') }}" class="text-sm font-semibold text-slate-300 hover:text-white transition-colors px-3 py-2">
                    Client Login
                </a>
                <a href="{{ route('start-project') }}" class="btn-base btn-primary btn-sm">
                    Start a Project
                </a>
            </div>

            <!-- Mobile Hamburger Toggle -->
            <div class="flex md:hidden items-center gap-3">
                <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-300 hover:text-white px-2 py-1 border border-slate-700 rounded">
                    Login
                </a>
                <button type="button" id="mobile-menu-btn" aria-expanded="false" aria-label="Toggle navigation menu" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Drawer -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-800 bg-slate-900 px-4 pt-3 pb-6 space-y-3">
        <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800 {{ request()->routeIs('home') ? 'bg-slate-800 text-blue-400 font-bold' : '' }}">Home</a>
        <a href="{{ route('services.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800 {{ request()->routeIs('services.*') ? 'bg-slate-800 text-blue-400 font-bold' : '' }}">Services</a>
        <a href="{{ route('industries.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800 {{ request()->routeIs('industries.*') ? 'bg-slate-800 text-blue-400 font-bold' : '' }}">Industries</a>
        <a href="{{ route('portfolio.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800 {{ request()->routeIs('portfolio.*') ? 'bg-slate-800 text-blue-400 font-bold' : '' }}">Portfolio</a>
        <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800 {{ request()->routeIs('about') ? 'bg-slate-800 text-blue-400 font-bold' : '' }}">About</a>
        <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-200 hover:bg-slate-800 {{ request()->routeIs('contact') ? 'bg-slate-800 text-blue-400 font-bold' : '' }}">Contact</a>
        <a href="{{ route('partners') }}" class="block px-3 py-2 rounded-md text-base font-semibold text-amber-300 hover:bg-slate-800 {{ request()->routeIs('partners*') ? 'bg-slate-800 text-amber-400 font-bold' : '' }}">Partner Program (20% Commission)</a>
        <div class="pt-4 border-t border-slate-800 flex flex-col gap-3">
            <a href="{{ route('login') }}" class="block text-center px-4 py-2.5 rounded-lg border border-slate-700 text-slate-200 font-semibold text-sm hover:bg-slate-800">
                Client Login Portal
            </a>
            <a href="{{ route('start-project') }}" class="block text-center btn-base btn-primary btn-sm">
                Start a Project
            </a>
        </div>
    </div>
</header>

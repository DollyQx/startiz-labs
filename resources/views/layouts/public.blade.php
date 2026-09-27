<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>@yield('title', 'Startiz Labs — Complete Digital Solutions for Businesses')</title>
    <meta name="description" content="@yield('meta_description', 'Startiz Labs builds websites, mobile apps, custom software, AI automation, CRM systems, e-commerce, and business software for startups, retailers, restaurants, institutes, and organizations.')">
    <link rel="canonical" href="@yield('canonical', request()->url())">
    
    <!-- Open Graph Metadata -->
    <meta property="og:title" content="@yield('og_title', 'Startiz Labs — One Place for Your Digital Solution')">
    <meta property="og:description" content="@yield('og_description', 'One place for your complete digital business solution: web apps, mobile solutions, AI automation, and custom software.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ request()->url() }}">
    
    <!-- Twitter Metadata -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Startiz Labs — One Place for Your Digital Solution')">
    <meta name="twitter:description" content="@yield('og_description', 'One place for your complete digital business solution: web apps, mobile solutions, AI automation, and custom software.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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

    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
</head>
<body class="flex flex-col min-h-screen bg-slate-50 text-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white">
    <!-- Skip to Content Accessibility Link -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded-md z-50">
        Skip to main content
    </a>

    <!-- Navbar Header -->
    <x-navbar />

    <!-- Main Page Body -->
    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Component -->
    <x-footer />

    <!-- Mobile Drawer & Interactivity Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            if (mobileMenuBtn && mobileMenu) {
                mobileMenuBtn.addEventListener('click', function() {
                    const isExpanded = mobileMenuBtn.getAttribute('aria-expanded') === 'true';
                    mobileMenuBtn.setAttribute('aria-expanded', !isExpanded);
                    mobileMenu.classList.toggle('hidden');
                });
            }
        });
    </script>
</body>
</html>

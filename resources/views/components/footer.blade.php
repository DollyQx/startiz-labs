<footer class="bg-slate-950 text-slate-400 border-t border-slate-900 pt-16 pb-12">
    <div class="container-custom">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 pb-12 border-b border-slate-900">
            <!-- Brand Column -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3 text-white font-extrabold text-xl tracking-wider">
                    <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center text-white font-black text-xs shadow-sm">
                        STZ
                    </div>
                    <span>STARTIZ LABS</span>
                </div>
                <p class="text-sm text-slate-400 leading-relaxed max-w-sm">
                    One place for your complete digital business solution. We build websites, mobile applications, business software, AI automation, and custom technology for growing businesses.
                </p>
                <div class="pt-2 flex items-center gap-4 text-xs font-semibold text-slate-400">
                    <a href="{{ config('services.social.linkedin', '#') }}" target="_blank" rel="noopener noreferrer" class="hover:text-blue-400 transition-colors">LinkedIn</a>
                    <span>&bull;</span>
                    <a href="{{ config('services.social.instagram', '#') }}" target="_blank" rel="noopener noreferrer" class="hover:text-pink-400 transition-colors">Instagram</a>
                    <span>&bull;</span>
                    <a href="{{ config('services.social.whatsapp', 'https://wa.me/910000000000') }}" target="_blank" rel="noopener noreferrer" class="hover:text-emerald-400 transition-colors">WhatsApp CTA</a>
                </div>
            </div>

            <!-- Services Links Column -->
            <div>
                <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Core Solutions</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">Website Development</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">Mobile App Development</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">AI Automation</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">CRM Development</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">E-Commerce & Retail</a></li>
                    <li><a href="{{ route('services.index') }}" class="hover:text-white transition-colors">Custom Business Software</a></li>
                </ul>
            </div>

            <!-- Industries Column -->
            <div>
                <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Target Customers</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('industries.index') }}" class="hover:text-white transition-colors">Startups</a></li>
                    <li><a href="{{ route('industries.index') }}" class="hover:text-white transition-colors">Retailers & Shops</a></li>
                    <li><a href="{{ route('industries.index') }}" class="hover:text-white transition-colors">Restaurants & Food</a></li>
                    <li><a href="{{ route('industries.index') }}" class="hover:text-white transition-colors">Institutes & Education</a></li>
                    <li><a href="{{ route('industries.index') }}" class="hover:text-white transition-colors">Small & Medium Businesses</a></li>
                </ul>
            </div>

            <!-- Company & Portals Column -->
            <div>
                <h4 class="text-white font-semibold text-sm uppercase tracking-wider mb-4">Quick Links</h4>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">About Startiz Labs</a></li>
                    <li><a href="{{ route('portfolio.index') }}" class="hover:text-white transition-colors">Portfolio & Past Work</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Contact Us</a></li>
                    <li><a href="{{ route('partners') }}" class="text-amber-400 hover:text-amber-300 font-semibold transition-colors flex items-center gap-1"><span>Partner Program</span> <span class="text-[10px] bg-amber-500/20 px-1 rounded border border-amber-500/30">20% Earn</span></a></li>
                    <li><a href="{{ route('partner.terms') }}" class="text-slate-400 hover:text-white transition-colors">Partner Terms & Conditions</a></li>
                    <li><a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-medium">Client Login Portal</a></li>
                    <li><a href="{{ route('start-project') }}" class="text-blue-400 hover:text-blue-300 font-medium">Start a Project</a></li>
                </ul>
            </div>
        </div>

        <!-- Bottom Copyright Row -->
        <div class="pt-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <p>&copy; {{ date('Y') }} Startiz Labs. All rights reserved. One Place for Complete Digital Business Solutions.</p>
            <div class="flex items-center gap-6">
                <span class="hover:text-slate-300 cursor-pointer">Privacy Policy</span>
                <span class="hover:text-slate-300 cursor-pointer">Terms of Service</span>
                <span class="hover:text-slate-300 cursor-pointer">Security</span>
            </div>
        </div>
    </div>
</footer>

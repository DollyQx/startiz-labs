@extends('layouts.public')

@section('title', 'Contact Startiz Labs — Software Consultation')
@section('meta_description', 'Get in touch with Startiz Labs for software engineering inquiries, custom digital solutions, and technical proposals.')

@section('content')
    <section class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="container-custom text-center max-w-3xl">
            <span class="badge-public mb-4 bg-blue-950 text-blue-400 border border-blue-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                Get In Touch
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                Connect with Our Technical Team
            </h1>
            <p class="text-lg text-slate-300 leading-relaxed">
                Have a software project in mind? Contact our engineering team for technical consultations and project estimates.
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container-custom max-w-5xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Info Column -->
                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900 mb-4">Contact Information</h2>
                        <p class="text-slate-600 leading-relaxed">
                            Reach out to discuss your technical requirements, architectural blueprints, or existing project enhancements.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 font-bold">
                                @
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Email Inquiries</h3>
                                <p class="text-sm text-slate-600 mt-1">Available via official client contact channels.</p>
                            </div>
                        </div>

                        <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0 font-bold">
                                #
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Project Requirements</h3>
                                <p class="text-sm text-slate-600 mt-1">Submit detailed project scope directly via our project portal.</p>
                                <a href="{{ route('start-project') }}" class="inline-block mt-2 text-xs font-bold text-blue-600 hover:underline">Start a Project &rarr;</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Column Shell -->
                <div class="p-8 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm">
                    <h3 class="text-xl font-bold text-slate-900 mb-6">Send a Direct Message</h3>
                    <form action="#" method="POST" class="space-y-4" onsubmit="event.preventDefault(); alert('For project inquiries, please use the Start a Project form.');">
                        @csrf
                        <div>
                            <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Your Name</label>
                            <input type="text" id="name" name="name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Address</label>
                            <input type="email" id="email" name="email" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label for="subject" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Subject</label>
                            <input type="text" id="subject" name="subject" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Message</label>
                            <textarea id="message" name="message" rows="4" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
                        </div>
                        <button type="submit" class="btn-base btn-primary w-full">
                            Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

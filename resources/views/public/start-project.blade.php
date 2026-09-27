@extends('layouts.public')

@section('title', 'Start a Project — Startiz Labs')
@section('meta_description', 'Submit your custom software project requirements to Startiz Labs for a comprehensive technical architecture blueprint and quotation.')

@section('content')
    <section class="py-16 md:py-24 bg-slate-900 text-white">
        <div class="container-custom text-center max-w-3xl">
            <span class="badge-public mb-4 bg-blue-950 text-blue-400 border border-blue-800 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                Initiate Project Discovery
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-6">
                Start Your Custom Software Project
            </h1>
            <p class="text-lg text-slate-300 leading-relaxed">
                Tell us about your digital solution requirements. Our engineering team will review your scope and provide a formal milestone breakdown and quotation.
            </p>
        </div>
    </section>

    <section class="py-20 bg-white">
        <div class="container-custom max-w-3xl">
            <div class="p-8 md:p-12 bg-slate-50 border border-slate-200 rounded-2xl shadow-sm space-y-8">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-2">Project Requirement Submission</h2>
                    <p class="text-sm text-slate-600">
                        Fill out the initial project details below or sign up for a client portal account to track requirement submissions, quotations, and project milestones in real-time.
                    </p>
                </div>

                <form action="#" method="POST" class="space-y-6" onsubmit="event.preventDefault(); alert('Requirement submitted! Our technical team will review your proposal.');">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="full_name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Full Name</label>
                            <input type="text" id="full_name" name="full_name" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label for="work_email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Email Address</label>
                            <input type="email" id="work_email" name="work_email" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Phone Number</label>
                            <input type="text" id="phone" name="phone" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                        </div>
                        <div>
                            <label for="solution_category" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Solution Category</label>
                            <select id="solution_category" name="solution_category" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                                <option value="web">Web Application Development</option>
                                <option value="app">Mobile Application (iOS/Android)</option>
                                <option value="lms">LMS & Examination System</option>
                                <option value="healthcare">Healthcare & Hospital Software</option>
                                <option value="hostel">Hostel & Mess Management</option>
                                <option value="retail">Retail POS & E-Commerce</option>
                                <option value="custom">Custom Enterprise ERP</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="project_summary" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Project Summary & Core Requirements</label>
                        <textarea id="project_summary" name="project_summary" rows="5" required placeholder="Describe your operational goals, key features, target timeline, or specific industry workflows..." class="w-full px-4 py-2.5 rounded-lg border border-slate-300 bg-white text-slate-900 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"></textarea>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row items-center gap-4">
                        <button type="submit" class="btn-base btn-primary btn-lg w-full sm:w-auto">
                            Submit Requirement Proposal
                        </button>
                        <a href="{{ route('register') }}" class="btn-base btn-outline btn-lg w-full sm:w-auto">
                            Register Client Account
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

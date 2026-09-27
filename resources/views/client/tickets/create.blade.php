@extends('layouts.client')

@section('title', 'Submit Support Ticket')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Navigation Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('client.tickets.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Support Tickets
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-5">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Create Support Ticket</h1>
            <p class="text-xs text-slate-500 mt-1">Submit a detailed technical request or inquiry. Our support team will review and respond promptly.</p>
        </div>

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs space-y-1">
                <span class="font-bold block">Please fix the following validation errors:</span>
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('client.tickets.store') }}" class="space-y-6">
            @csrf

            <!-- Subject -->
            <div>
                <label for="subject" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Ticket Subject <span class="text-rose-500">*</span></label>
                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required placeholder="e.g. Issue with SSL deployment on Staging environment" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
            </div>

            <!-- Category, Priority, Project Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <!-- Category -->
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category <span class="text-rose-500">*</span></label>
                    <select id="category" name="category" required class="w-full px-3 py-3 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->value }}" {{ old('category') === $cat->value ? 'selected' : '' }}>
                                {{ $cat->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Priority -->
                <div>
                    <label for="priority" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Priority Level <span class="text-rose-500">*</span></label>
                    <select id="priority" name="priority" required class="w-full px-3 py-3 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                        @foreach($priorities as $pri)
                            <option value="{{ $pri->value }}" {{ old('priority', 'medium') === $pri->value ? 'selected' : '' }}>
                                {{ $pri->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Related Project (Optional) -->
                <div>
                    <label for="project_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Related Project <span class="text-slate-400 font-normal">(Optional)</span></label>
                    <select id="project_id" name="project_id" class="w-full px-3 py-3 rounded-xl border border-slate-200 text-sm bg-white text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                        <option value="">-- None (General Account) --</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>
                                {{ $proj->title }} ({{ $proj->reference_number }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Description / Details -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Detailed Description <span class="text-rose-500">*</span></label>
                <textarea id="description" name="description" rows="6" required placeholder="Provide clear details, steps to reproduce, or requirements for our support team..." class="w-full p-4 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">{{ old('description') }}</textarea>
            </div>

            <!-- Action buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('client.tickets.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                    Submit Support Ticket
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

@extends('layouts.client')

@section('title', 'Submit Change Request')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Navigation Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('client.change-requests.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Change Requests
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/90 shadow-sm space-y-6">
        <div class="border-b border-slate-100 pb-5">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Submit Change Request</h1>
            <p class="text-xs text-slate-500 mt-1">Request scope additions, feature enhancements, or architecture adjustments for an existing project.</p>
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

        <form method="POST" action="{{ route('client.change-requests.store') }}" class="space-y-6">
            @csrf

            <!-- Project & Priority Row -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="sm:col-span-2">
                    <label for="project_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Target Project <span class="text-rose-500">*</span></label>
                    <select id="project_id" name="project_id" required class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                        <option value="">-- Select Your Active Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->title }} ({{ $project->reference_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="priority" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Priority Level <span class="text-rose-500">*</span></label>
                    <select id="priority" name="priority" required class="w-full px-3 py-3 rounded-xl border border-slate-200 text-sm bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
                        @foreach($priorities as $pri)
                            <option value="{{ $pri->value }}" {{ old('priority', 'medium') === $pri->value ? 'selected' : '' }}>
                                {{ $pri->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Change Request Title <span class="text-rose-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. Add Multi-Currency Support & Export to PDF feature" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
            </div>

            <!-- Reason / Business Justification (Optional) -->
            <div>
                <label for="reason" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Business Reason / Justification <span class="text-slate-400 font-normal">(Optional)</span></label>
                <input type="text" id="reason" name="reason" value="{{ old('reason') }}" placeholder="e.g. Expand market reach into European Union regional clients" class="w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">
            </div>

            <!-- Detailed Description -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Detailed Scope Description <span class="text-rose-500">*</span></label>
                <textarea id="description" name="description" rows="6" required placeholder="Describe the requested feature, functional specifications, or architectural adjustments in detail..." class="w-full p-4 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition-all">{{ old('description') }}</textarea>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('client.change-requests.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-500/20 transition-all">
                    Submit Change Request
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

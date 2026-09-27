@extends('layouts.admin')

@section('title', 'Edit Project: ' . $project->reference_number)
@section('breadcrumb', 'Projects / ' . $project->reference_number . ' / Edit')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.projects.show', $project->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-2">
                &larr; Back to Project Workspace
            </a>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Edit Project {{ $project->reference_number }}</h1>
            <p class="text-sm text-slate-500 mt-1">Update project scope, parameters, client mapping, and timeline dates.</p>
        </div>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-xl border border-slate-200 shadow-sm">
        <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Client & Categorization Section -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">
                    Client & Domain Categorization
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <!-- Client -->
                    <div>
                        <label for="client_id" class="block text-xs font-bold text-slate-700 mb-1">
                            Client Account <span class="text-rose-500">*</span>
                        </label>
                        <select name="client_id" id="client_id" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" {{ (string)old('client_id', $project->client_id) === (string)$c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Service -->
                    <div>
                        <label for="service_id" class="block text-xs font-bold text-slate-700 mb-1">Target Service</label>
                        <select name="service_id" id="service_id" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Service (Optional)</option>
                            @foreach($services as $s)
                                <option value="{{ $s->id }}" {{ (string)old('service_id', $project->service_id) === (string)$s->id ? 'selected' : '' }}>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Industry -->
                    <div>
                        <label for="industry_id" class="block text-xs font-bold text-slate-700 mb-1">Industry Sector</label>
                        <select name="industry_id" id="industry_id" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Industry (Optional)</option>
                            @foreach($industries as $ind)
                                <option value="{{ $ind->id }}" {{ (string)old('industry_id', $project->industry_id) === (string)$ind->id ? 'selected' : '' }}>
                                    {{ $ind->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Project Details -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">
                    Project Scope & Lifecycle
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Title -->
                    <div class="sm:col-span-2">
                        <label for="title" class="block text-xs font-bold text-slate-700 mb-1">
                            Project Title <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 mb-1">
                            Status <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($statuses as $st)
                                <option value="{{ $st->value }}" {{ old('status', $project->status->value) === $st->value ? 'selected' : '' }}>
                                    {{ $st->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority" class="block text-xs font-bold text-slate-700 mb-1">
                            Priority Level <span class="text-rose-500">*</span>
                        </label>
                        <select name="priority" id="priority" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @foreach($priorities as $pr)
                                <option value="{{ $pr->value }}" {{ old('priority', $project->priority->value) === $pr->value ? 'selected' : '' }}>
                                    {{ $pr->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="sm:col-span-2">
                        <label for="description" class="block text-xs font-bold text-slate-700 mb-1">Project Summary / Scope</label>
                        <textarea name="description" id="description" rows="3" class="w-full text-xs p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description', $project->description) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Schedule & Valuation -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">
                    Schedule & Financial Valuation
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-xs font-bold text-slate-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date ? $project->start_date->format('Y-m-d') : '') }}" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Expected Completion Date -->
                    <div>
                        <label for="expected_completion_date" class="block text-xs font-bold text-slate-700 mb-1">Expected Completion</label>
                        <input type="date" name="expected_completion_date" id="expected_completion_date" value="{{ old('expected_completion_date', $project->expected_completion_date ? $project->expected_completion_date->format('Y-m-d') : '') }}" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Actual Completion Date -->
                    <div>
                        <label for="actual_completion_date" class="block text-xs font-bold text-slate-700 mb-1">Actual Completion</label>
                        <input type="date" name="actual_completion_date" id="actual_completion_date" value="{{ old('actual_completion_date', $project->actual_completion_date ? $project->actual_completion_date->format('Y-m-d') : '') }}" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <!-- Estimated Value -->
                    <div>
                        <label for="estimated_value" class="block text-xs font-bold text-slate-700 mb-1">Estimated Value (₹)</label>
                        <input type="number" step="0.01" min="0" name="estimated_value" id="estimated_value" value="{{ old('estimated_value', $project->estimated_value) }}" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Internal Notes -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200 pb-2">
                    Internal Delivery Notes
                </h3>
                <div>
                    <textarea name="notes" id="notes" rows="2" class="w-full text-xs p-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('notes', $project->notes) }}</textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.projects.show', $project->id) }}" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-sm transition-colors">
                    Update Project Details
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

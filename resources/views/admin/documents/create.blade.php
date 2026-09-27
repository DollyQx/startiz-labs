@extends('layouts.admin')

@section('title', 'Upload Document')
@section('breadcrumb', 'Upload Document')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Upload New Document</h1>
            <p class="text-sm text-slate-500 mt-1">Upload files securely to private storage with role-based access control.</p>
        </div>
        <a href="{{ route('admin.documents.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-2 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
            Back to Documents
        </a>
    </div>

    <!-- Upload Form Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Client & Project Context -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Client Selection -->
                <div>
                    <label for="client_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Target Client <span class="text-rose-500">*</span>
                    </label>
                    <select name="client_id" id="client_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">-- Select Client Account --</option>
                        @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ (old('client_id', $selectedClientId) == $client->id) ? 'selected' : '' }}>
                                {{ $client->name }} ({{ $client->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Select client recipient for this document.</p>
                </div>

                <!-- Project Selection -->
                <div>
                    <label for="project_id" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Associated Project
                    </label>
                    <select name="project_id" id="project_id" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        <option value="">-- Optional: Select Project --</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}" {{ (old('project_id', $selectedProjectId) == $project->id) ? 'selected' : '' }}>
                                {{ $project->title }} ({{ $project->client?->name ?? 'No Client' }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">Project and Client MUST belong to the same account.</p>
                </div>
            </div>

            <!-- Type & Visibility -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Document Type -->
                <div>
                    <label for="document_type" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Document Category <span class="text-rose-500">*</span>
                    </label>
                    <select name="document_type" id="document_type" required class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->value }}" {{ old('document_type') == $type->value ? 'selected' : '' }}>
                                {{ $type->label() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Document Visibility -->
                <div>
                    <label for="visibility" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                        Access Level <span class="text-rose-500">*</span>
                    </label>
                    <select name="visibility" id="visibility" required class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                        @foreach($visibilityOptions as $visibility)
                            <option value="{{ $visibility->value }}" {{ old('visibility', \App\Enums\DocumentVisibility::PRIVATE->value) == $visibility->value ? 'selected' : '' }}>
                                {{ $visibility->label() }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1">
                        <strong class="text-amber-600">Internal:</strong> Only staff can view. <strong class="text-emerald-600">Client Accessible:</strong> Client can view & download.
                    </p>
                </div>
            </div>

            <!-- Description / Notes -->
            <div>
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                    Description & Notes
                </label>
                <textarea name="description" id="description" rows="3" placeholder="Provide context, revision notes, or reference details..." class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">{{ old('description') }}</textarea>
            </div>

            <!-- Secure File Upload -->
            <div>
                <label for="file" class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-2">
                    Select File <span class="text-rose-500">*</span>
                </label>
                <div class="border-2 border-dashed border-slate-200 hover:border-blue-500 rounded-2xl p-6 text-center transition-colors bg-slate-50/50">
                    <input type="file" name="file" id="file" required class="w-full text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700">
                    <p class="text-[11px] text-slate-400 mt-2">
                        Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, PNG, JPG, JPEG, ZIP (Max 20 MB).
                    </p>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.documents.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-4 py-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 px-6 py-2.5 rounded-xl shadow-sm transition-colors">
                    Upload Secure Document
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

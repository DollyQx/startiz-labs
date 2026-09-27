@extends('layouts.admin')

@section('title', 'Document Details — ' . $document->reference_number)
@section('breadcrumb', 'Document Details')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Navigation & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-mono font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-md border border-blue-200">
                    {{ $document->reference_number }}
                </span>
                @if($document->visibility->isClientVisible())
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Client Accessible
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        Internal / Private
                    </span>
                @endif
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-2">{{ $document->original_filename }}</h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.documents.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-3.5 py-2 rounded-xl border border-slate-200 hover:bg-slate-50 transition-colors">
                Back to Documents
            </a>
            <a href="{{ route('admin.documents.download', $document) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download File
            </a>
        </div>
    </div>

    <!-- Main Detail Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left Details (2 Cols) -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Overview Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 pb-3 border-b border-slate-100">Document Information</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div>
                        <span class="block text-slate-400 font-semibold mb-0.5">Category / Type</span>
                        <span class="font-bold text-slate-800">{{ $document->document_type->label() }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-semibold mb-0.5">File Size</span>
                        <span class="font-bold text-slate-800">{{ number_format($document->file_size / 1024, 2) }} KB</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-semibold mb-0.5">MIME Type</span>
                        <span class="font-mono text-slate-700 bg-slate-100 px-2 py-0.5 rounded text-[11px] inline-block">{{ $document->mime_type ?? 'N/A' }}</span>
                    </div>

                    <div>
                        <span class="block text-slate-400 font-semibold mb-0.5">Uploaded On</span>
                        <span class="font-bold text-slate-800">{{ $document->created_at->format('F d, Y \a\t H:i') }}</span>
                    </div>
                </div>

                <!-- Contextual Associations -->
                <div class="pt-4 border-t border-slate-100 space-y-3">
                    <h4 class="text-xs font-bold text-slate-700">Associated Client & Project</h4>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Client</span>
                            @if($document->client)
                                <a href="{{ route('admin.clients.show', $document->client) }}" class="font-bold text-blue-600 hover:underline text-xs mt-0.5 block">
                                    {{ $document->client->name }}
                                </a>
                                <span class="text-[11px] text-slate-500 block">{{ $document->client->email }}</span>
                            @else
                                <span class="text-xs text-slate-400">No Associated Client</span>
                            @endif
                        </div>

                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Project</span>
                            @if($document->project)
                                <a href="{{ route('admin.projects.show', $document->project) }}" class="font-bold text-blue-600 hover:underline text-xs mt-0.5 block">
                                    {{ $document->project->title }}
                                </a>
                                <span class="text-[11px] text-slate-500 block">Ref: {{ $document->project->reference_number }}</span>
                            @else
                                <span class="text-xs text-slate-400">Global / Direct Document</span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Notes / Description -->
                @if($document->description)
                    <div class="pt-4 border-t border-slate-100">
                        <h4 class="text-xs font-bold text-slate-700 mb-1">Description & Notes</h4>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-700 leading-relaxed">
                            {{ $document->description }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right Operational Actions (1 Col) -->
        <div class="space-y-6">

            <!-- Upload Audit Card -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 pb-2 border-b border-slate-100">Upload Metadata</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block font-medium">Uploaded By</span>
                        <span class="font-bold text-slate-800">{{ $document->uploadedBy?->name ?? 'System Admin' }}</span>
                    </div>

                    <div>
                        <span class="text-slate-400 block font-medium">Storage Path</span>
                        <span class="font-mono text-[10px] text-slate-500 break-all bg-slate-50 p-2 rounded-lg border border-slate-100 block">
                            {{ $document->storage_path }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Quick Visibility Toggle Form -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500 pb-2 border-b border-slate-100">Visibility Setting</h3>

                <form action="{{ route('admin.documents.visibility', $document) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="visibility_select" class="block text-xs font-semibold text-slate-600 mb-1">Access Level</label>
                        <select name="visibility" id="visibility_select" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                            <option value="private" {{ $document->visibility->value === 'private' ? 'selected' : '' }}>Internal / Private</option>
                            <option value="client" {{ $document->visibility->value === 'client' ? 'selected' : '' }}>Client Accessible</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 py-2 rounded-xl transition-colors">
                        Update Visibility
                    </button>
                </form>
            </div>

            <!-- Delete Form -->
            <div class="bg-rose-50/50 rounded-2xl border border-rose-200 p-6 space-y-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-rose-800">Danger Zone</h3>
                <p class="text-xs text-rose-600">Permanently delete this document and purge its file from private storage.</p>
                <form action="{{ route('admin.documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this document and its file?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 py-2.5 rounded-xl transition-colors shadow-sm">
                        Delete Document Permanently
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection

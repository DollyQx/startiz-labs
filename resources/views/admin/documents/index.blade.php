@extends('layouts.admin')

@section('title', 'Document Management')
@section('breadcrumb', 'Documents')

@section('content')
<div class="space-y-6">

    <!-- Top Header & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Document Management</h1>
            <p class="text-sm text-slate-500 mt-1">Manage private and client-accessible project documentation securely.</p>
        </div>
        <div>
            <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Upload Document
            </a>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
        <form method="GET" action="{{ route('admin.documents.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4">
            
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Filename, ref # or notes..." class="w-full text-xs rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
            </div>

            <!-- Client Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Client</label>
                <select name="client_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                    <option value="">All Clients</option>
                    @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                            {{ $client->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Project Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Project</label>
                <select name="project_id" class="w-full text-xs rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                    <option value="">All Projects</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Document Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Type</label>
                <select name="document_type" class="w-full text-xs rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                    <option value="">All Types</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->value }}" {{ request('document_type') == $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Visibility Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Visibility</label>
                <select name="visibility" class="w-full text-xs rounded-lg border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2 px-3">
                    <option value="">All Visibility</option>
                    @foreach($visibilityOptions as $vis)
                        <option value="{{ $vis->value }}" {{ request('visibility') == $vis->value ? 'selected' : '' }}>
                            {{ $vis->label() }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="lg:col-span-6 flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                <a href="{{ route('admin.documents.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors">
                    Reset
                </a>
                <button type="submit" class="text-xs font-semibold text-white bg-slate-900 hover:bg-slate-800 px-4 py-1.5 rounded-lg transition-colors">
                    Filter Results
                </button>
            </div>
        </form>
    </div>

    <!-- Documents Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($documents->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">No documents found</h3>
                <p class="text-xs text-slate-500 mt-1">No document records matched your filter parameters.</p>
                <div class="mt-4">
                    <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 px-3 py-2 rounded-lg">
                        Upload first document
                    </a>
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4">Ref # & Document</th>
                            <th class="py-3.5 px-4">Client / Project</th>
                            <th class="py-3.5 px-4">Type</th>
                            <th class="py-3.5 px-4">Visibility</th>
                            <th class="py-3.5 px-4">Size & Date</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @foreach($documents as $doc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.documents.show', $doc) }}" class="font-bold text-slate-900 hover:text-blue-600 truncate max-w-[220px] block" title="{{ $doc->original_filename }}">
                                                {{ $doc->original_filename }}
                                            </a>
                                            <span class="text-[10px] font-mono text-slate-400 block">{{ $doc->reference_number }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <div class="space-y-0.5">
                                        <div class="font-semibold text-slate-800">
                                            {{ $doc->client?->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500">
                                            {{ $doc->project?->title ?? 'Global Document' }}
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $doc->document_type->label() }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($doc->visibility->isClientVisible())
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            Client Visible
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                            Internal / Private
                                        </span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-slate-600">
                                    <div>{{ number_format($doc->file_size / 1024, 1) }} KB</div>
                                    <div class="text-[10px] text-slate-400">{{ $doc->created_at->format('M d, Y H:i') }}</div>
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('admin.documents.show', $doc) }}" class="p-1.5 text-slate-500 hover:text-slate-900 rounded-md hover:bg-slate-100 transition-colors" title="View Details">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>

                                        <a href="{{ route('admin.documents.download', $doc) }}" class="p-1.5 text-blue-600 hover:text-blue-800 rounded-md hover:bg-blue-50 transition-colors" title="Download File">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        </a>

                                        <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this document and its stored file?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-rose-500 hover:text-rose-700 rounded-md hover:bg-rose-50 transition-colors" title="Delete Document">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t border-slate-200">
                {{ $documents->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

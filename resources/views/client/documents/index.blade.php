@extends('layouts.client')

@section('title', 'My Documents')

@section('content')
<div class="space-y-6">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Project & Account Documents</h1>
            <p class="text-sm text-slate-500 mt-1">Access and download your project contracts, proposals, invoices, and deliverables.</p>
        </div>
    </div>

    <!-- Filter & Search Bar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('client.documents.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            
            <!-- Search -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Search Documents</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by document name or reference number..." class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
            </div>

            <!-- Document Type Filter -->
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Category</label>
                <select name="document_type" onchange="this.form.submit()" class="w-full text-xs rounded-xl border-slate-200 focus:border-blue-500 focus:ring-blue-500 py-2.5 px-3">
                    <option value="">All Categories</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type->value }}" {{ request('document_type') == $type->value ? 'selected' : '' }}>
                            {{ $type->label() }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Documents Data List -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        @if($documents->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">No documents available</h3>
                <p class="text-xs text-slate-500 mt-1">There are currently no visible documents associated with your account.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                            <th class="py-3.5 px-4">Document Name</th>
                            <th class="py-3.5 px-4">Category</th>
                            <th class="py-3.5 px-4">Associated Project</th>
                            <th class="py-3.5 px-4">Size & Date</th>
                            <th class="py-3.5 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @foreach($documents as $doc)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold flex-shrink-0 mt-0.5">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <a href="{{ route('client.documents.show', $doc) }}" class="font-bold text-slate-900 hover:text-blue-600 truncate max-w-[240px] block" title="{{ $doc->original_filename }}">
                                                {{ $doc->original_filename }}
                                            </a>
                                            <span class="text-[10px] font-mono text-slate-400 block">{{ $doc->reference_number }}</span>
                                        </div>
                                    </div>
                                </td>

                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ $doc->document_type->label() }}
                                    </span>
                                </td>

                                <td class="py-3.5 px-4">
                                    @if($doc->project)
                                        <span class="font-semibold text-slate-800">{{ $doc->project->title }}</span>
                                    @else
                                        <span class="text-slate-400">Account Level</span>
                                    @endif
                                </td>

                                <td class="py-3.5 px-4 text-slate-600">
                                    <div class="font-semibold text-slate-800">{{ number_format($doc->file_size / 1024, 1) }} KB</div>
                                    <div class="text-[10px] text-slate-400">{{ $doc->created_at->format('M d, Y') }}</div>
                                </td>

                                <td class="py-3.5 px-4 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        <a href="{{ route('client.documents.show', $doc) }}" class="px-3 py-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                            Details
                                        </a>

                                        <a href="{{ route('client.documents.download', $doc) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            Download
                                        </a>
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

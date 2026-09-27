@extends('layouts.client')

@section('title', 'Document — ' . $document->original_filename)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Header & Back Navigation -->
    <div class="flex items-center justify-between">
        <a href="{{ route('client.documents.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200/80 px-3.5 py-2 rounded-xl shadow-sm hover:bg-slate-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to Documents
        </a>

        <a href="{{ route('client.documents.download', $document) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-sm transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            Download Document
        </a>
    </div>

    <!-- Main Content Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
        
        <!-- Document Header Icon & Filename -->
        <div class="flex items-start gap-4 pb-6 border-b border-slate-100">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-extrabold flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <div class="space-y-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                    {{ $document->document_type->label() }}
                </span>
                <h1 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $document->original_filename }}</h1>
                <p class="text-xs font-mono text-slate-400">Reference: {{ $document->reference_number }}</p>
            </div>
        </div>

        <!-- Metadata Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Associated Project</span>
                @if($document->project)
                    <a href="{{ route('client.projects.show', $document->project) }}" class="font-bold text-blue-600 hover:underline">
                        {{ $document->project->title }}
                    </a>
                @else
                    <span class="font-semibold text-slate-700">Account Document</span>
                @endif
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">File Size</span>
                <span class="font-bold text-slate-800">{{ number_format($document->file_size / 1024, 2) }} KB</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Uploaded Date</span>
                <span class="font-bold text-slate-800">{{ $document->created_at->format('F d, Y') }}</span>
            </div>

            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">File Type</span>
                <span class="font-bold text-slate-800 uppercase">{{ pathinfo($document->original_filename, PATHINFO_EXTENSION) }}</span>
            </div>
        </div>

        <!-- Description (If present) -->
        @if($document->description)
            <div class="pt-4 border-t border-slate-100">
                <h4 class="text-xs font-bold text-slate-700 mb-2">Description & Notes</h4>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-700 leading-relaxed">
                    {{ $document->description }}
                </div>
            </div>
        @endif

        <!-- Action Footer -->
        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
            <span class="text-xs text-slate-400">Verified Client Document</span>
            <a href="{{ route('client.documents.download', $document) }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download File
            </a>
        </div>

    </div>

</div>
@endsection

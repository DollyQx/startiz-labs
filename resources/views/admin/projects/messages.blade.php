@extends('layouts.admin')

@section('title', 'Project Messages - ' . $project->title)

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-700/60 pb-5">
        <div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('admin.projects.show', $project->id) }}" class="text-slate-400 hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-white tracking-tight">Project Communication</h1>
                <span class="px-2.5 py-0.5 text-xs font-semibold rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                    {{ $project->reference_number }}
                </span>
            </div>
            <p class="text-sm text-slate-400 mt-1 ml-8">Direct project messaging with {{ $project->client?->name ?? 'Client' }}</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.projects.show', $project->id) }}" class="px-4 py-2 text-sm font-medium bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-lg transition-colors">
                Back to Workspace
            </a>
        </div>
    </div>

    <!-- Status Alert -->
    @if(session('status'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Chat Container -->
    <div class="bg-slate-800/80 border border-slate-700/60 rounded-xl overflow-hidden shadow-xl flex flex-col min-h-[500px]">
        <!-- Messages Feed -->
        <div class="p-6 overflow-y-auto space-y-6 flex-1 max-h-[600px] bg-slate-900/40">
            @forelse($messages as $msg)
                @php
                    $isStaff = $msg->sender && $msg->sender->isAdmin();
                    $isCurrentUser = (int) $msg->sender_id === (int) auth()->id();
                @endphp
                
                <div class="flex flex-col {{ $isCurrentUser ? 'items-end' : 'items-start' }}">
                    <div class="flex items-center space-x-2 mb-1.5 px-1">
                        <span class="text-xs font-semibold {{ $isStaff ? 'text-indigo-400' : 'text-emerald-400' }}">
                            {{ $msg->sender?->name ?? 'Unknown User' }}
                        </span>
                        <span class="text-[10px] px-2 py-0.5 rounded-full {{ $isStaff ? 'bg-indigo-500/20 text-indigo-300' : 'bg-emerald-500/20 text-emerald-300' }}">
                            {{ $isStaff ? ucfirst(str_replace('_', ' ', is_object($msg->sender->role) ? $msg->sender->role->value : $msg->sender->role)) : 'Client' }}
                        </span>
                        <span class="text-[11px] text-slate-500 font-mono">
                            {{ $msg->created_at->format('M d, H:i') }}
                        </span>
                    </div>

                    <div class="max-w-xl rounded-2xl p-4 text-sm shadow-md {{ $isCurrentUser ? 'bg-indigo-600 text-white rounded-tr-none' : ($isStaff ? 'bg-slate-700/90 text-slate-100 rounded-tl-none border border-slate-600/50' : 'bg-slate-800 text-slate-100 rounded-tl-none border border-slate-700') }}">
                        @if($msg->message)
                            <div class="leading-relaxed whitespace-pre-wrap">{!! nl2br(e($msg->message)) !!}</div>
                        @endif

                        @if($msg->attachment_path)
                            <div class="mt-3 pt-2 border-t border-white/20">
                                <a href="{{ route('admin.projects.messages.download', [$project->id, $msg->id]) }}" 
                                    class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-lg bg-black/20 hover:bg-black/30 text-xs font-medium text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="truncate max-w-[200px]">{{ $msg->original_filename }}</span>
                                    <span class="opacity-75">({{ number_format($msg->file_size / 1024, 1) }} KB)</span>
                                </a>
                            </div>
                        @endif
                    </div>

                    <div class="text-[10px] text-slate-500 mt-1 px-1">
                        @if($msg->isReadBy($project->client))
                            <span class="text-emerald-400 font-medium">✓ Read by client</span>
                        @else
                            <span class="text-slate-500">Unread by client</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-16">
                    <div class="w-12 h-12 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center mx-auto text-slate-500 mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                    </div>
                    <p class="text-slate-400 font-medium text-sm">No messages in this project conversation yet.</p>
                    <p class="text-slate-500 text-xs mt-1">Start the conversation below.</p>
                </div>
            @endforelse
        </div>

        @if($messages->hasPages())
            <div class="px-6 py-3 border-t border-slate-700/60 bg-slate-900/60">
                {{ $messages->links() }}
            </div>
        @endif

        <!-- Message Composer Form -->
        <div class="p-4 bg-slate-800 border-t border-slate-700/60">
            <form method="POST" action="{{ route('admin.projects.messages.store', $project->id) }}" enctype="multipart/form-data" class="space-y-3">
                @csrf
                <div>
                    <textarea name="message" rows="3" placeholder="Type your project message here..." 
                        class="w-full bg-slate-900/90 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 resize-none">{{ old('message') }}</textarea>
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-1">
                    <div>
                        <label for="attachment" class="inline-flex items-center space-x-2 px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs font-medium text-slate-300 hover:text-white cursor-pointer transition-colors">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                            <span>Attach File (Max 10MB)</span>
                        </label>
                        <input type="file" name="attachment" id="attachment" class="hidden" onchange="document.getElementById('filename_display').textContent = this.files[0] ? this.files[0].name : '';">
                        <span id="filename_display" class="text-xs text-slate-400 ml-2 font-mono"></span>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center space-x-2 px-6 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold transition-colors shadow-lg">
                        <span>Send Message</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

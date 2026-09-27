@extends('layouts.client')

@section('title', 'Project Activity & Timeline')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Project Activity & History</h1>
            <p class="text-sm text-gray-500 mt-1">Review verified activity, milestone updates, quotations, invoices, and ticket events.</p>
        </div>
        
        <!-- Filter by Project -->
        @if(count($projects) > 0)
            <form method="GET" action="{{ route('client.activity.index') }}" class="flex items-center space-x-2">
                <select name="project_id" onchange="this.form.submit()" class="bg-white border border-gray-300 rounded-lg px-3.5 py-2 text-sm text-gray-700 font-medium shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All My Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ (string) $selectedProjectId === (string) $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                    @endforeach
                </select>
            </form>
        @endif
    </div>

    <!-- Timeline Container -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        @if(count($activities) > 0)
            <div class="relative pl-6 space-y-8 before:absolute before:left-2.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-gray-200">
                @foreach($activities as $activity)
                    <div class="relative flex items-start space-x-4">
                        <!-- Timeline Icon Node -->
                        <div class="absolute -left-6 mt-1 flex items-center justify-center w-5 h-5 rounded-full ring-4 ring-white bg-indigo-600 text-white">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                        </div>

                        <!-- Content Card -->
                        <div class="flex-1 bg-gray-50/80 border border-gray-200/80 rounded-lg p-4 shadow-2xs">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1">
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        {{ $activity->action_label }}
                                    </span>
                                    @if($activity->project)
                                        <span class="text-xs font-medium text-gray-500">• {{ $activity->project->title }}</span>
                                    @endif
                                </div>
                                <time class="text-xs text-gray-400 font-medium">{{ $activity->created_at->format('M d, Y \a\t h:i A') }} ({{ $activity->created_at->diffForHumans() }})</time>
                            </div>

                            <p class="text-sm font-medium text-gray-800 mt-2">{{ $activity->description }}</p>

                            @if($activity->actor)
                                <div class="text-xs text-gray-500 mt-2">
                                    By <span class="font-medium text-gray-700">{{ $activity->actor->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($activities->hasPages())
                <div class="mt-8 pt-4 border-t border-gray-100">
                    {{ $activities->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No activity recorded yet</h3>
                <p class="mt-1 text-sm text-gray-500">Activity and milestone updates will appear here as your project progresses.</p>
            </div>
        @endif
    </div>
</div>
@endsection

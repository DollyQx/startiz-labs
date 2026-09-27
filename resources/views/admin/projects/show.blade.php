@extends('layouts.admin')

@section('title', 'Project Workspace: ' . $project->reference_number)
@section('breadcrumb', 'Projects / ' . $project->reference_number)

@section('content')
<div class="space-y-6">
    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.projects.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-700 mb-2">
                &larr; Back to Project Directory
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 tracking-tight font-mono">{{ $project->reference_number }}</h1>
                <!-- Status Badge -->
                <span class="inline-flex px-3 py-0.5 text-xs font-bold rounded-full border
                    {{ $project->status->value === 'completed' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : '' }}
                    {{ $project->status->value === 'cancelled' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                    {{ $project->status->value === 'on_hold' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                    {{ in_array($project->status->value, ['planning', 'approved', 'in_progress', 'testing', 'client_review', 'deployment']) ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                ">
                    {{ $project->status->label() }}
                </span>
                <!-- Priority Badge -->
                <span class="inline-flex px-2.5 py-0.5 text-[10px] font-bold uppercase rounded border
                    {{ $project->priority->value === 'urgent' ? 'bg-rose-100 text-rose-800 border-rose-200' : '' }}
                    {{ $project->priority->value === 'high' ? 'bg-amber-100 text-amber-800 border-amber-200' : '' }}
                    {{ $project->priority->value === 'medium' ? 'bg-blue-100 text-blue-800 border-blue-200' : '' }}
                    {{ $project->priority->value === 'low' ? 'bg-slate-100 text-slate-700 border-slate-200' : '' }}
                ">
                    {{ $project->priority->label() }} Priority
                </span>
            </div>
            <h2 class="text-lg font-semibold text-slate-800 mt-1">{{ $project->title }}</h2>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.projects.review', $project->id) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Review & Sign-Off
            </a>
            <a href="{{ route('admin.projects.messages', $project->id) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs rounded-xl transition-colors shadow-sm flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Project Messages
            </a>
            <a href="{{ route('admin.projects.edit', $project->id) }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold text-xs rounded-xl transition-colors border border-slate-200">
                Edit Project Details
            </a>
        </div>
    </div>

    <!-- Status Lifecycle Stage Bar -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm space-y-3">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Update Project Lifecycle Status</h3>
        
        <form action="{{ route('admin.projects.status', $project->id) }}" method="POST" class="flex flex-wrap items-center gap-2">
            @csrf
            @method('PATCH')
            
            @foreach($projectStatuses as $st)
                <button type="submit" name="status" value="{{ $st->value }}" class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all border {{ $project->status->value === $st->value ? 'bg-blue-600 text-white border-blue-600 shadow-sm ring-2 ring-blue-300' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                    {{ $st->label() }}
                </button>
            @endforeach
        </form>
    </div>

    <!-- Main Workspace Grid (Overview & Metrics) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Overview Card (2/3 width) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V9m0 0h5m-5 0H7"/></svg>
                Project Overview & Specifications
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <span class="text-slate-500 font-semibold uppercase block">Client Account</span>
                    @if($project->client)
                        <a href="{{ route('admin.clients.show', $project->client->id) }}" class="font-bold text-blue-600 hover:underline text-sm">
                            {{ $project->client->name }}
                        </a>
                        <span class="text-slate-500 block">({{ $project->client->email }})</span>
                    @else
                        <span class="font-medium text-slate-400">Unassigned</span>
                    @endif
                </div>

                <div>
                    <span class="text-slate-500 font-semibold uppercase block">Target Service</span>
                    <span class="font-bold text-slate-900 text-sm">{{ $project->service->name ?? 'General Web Development' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-semibold uppercase block">Industry Sector</span>
                    <span class="font-medium text-slate-800">{{ $project->industry->name ?? 'General Industry' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-semibold uppercase block">Financial Value</span>
                    <span class="font-bold text-emerald-700 text-sm">₹{{ number_format($project->estimated_value, 2) }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-semibold uppercase block">Start Date</span>
                    <span class="font-medium text-slate-900">{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Not Set' }}</span>
                </div>

                <div>
                    <span class="text-slate-500 font-semibold uppercase block">Expected Completion</span>
                    <span class="font-medium text-slate-900">{{ $project->expected_completion_date ? $project->expected_completion_date->format('M d, Y') : 'Not Set' }}</span>
                </div>

                @if($project->actual_completion_date)
                    <div>
                        <span class="text-slate-500 font-semibold uppercase block">Actual Completion</span>
                        <span class="font-bold text-emerald-600">{{ $project->actual_completion_date->format('M d, Y') }}</span>
                    </div>
                @endif
            </div>

            @if($project->description)
                <div class="pt-3 border-t border-slate-100">
                    <span class="text-slate-500 font-semibold uppercase block text-xs mb-1">Scope & Description</span>
                    <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-200">
                        {{ $project->description }}
                    </p>
                </div>
            @endif
        </div>

        <!-- Progress Summary Metrics Card (1/3 width) -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Progress Overview
            </h3>

            <!-- Calculated Percentage Bar -->
            <div class="space-y-1.5">
                <div class="flex justify-between items-center text-xs font-bold">
                    <span class="text-slate-700">Delivery Completion</span>
                    <span class="text-blue-600">{{ $progressPercentage }}%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%"></div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs pt-2">
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-slate-500 font-semibold block">Total Milestones</span>
                    <span class="text-lg font-bold text-slate-900">{{ $totalMilestones }}</span>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-200">
                    <span class="text-emerald-800 font-semibold block">Completed Milestones</span>
                    <span class="text-lg font-bold text-emerald-700">{{ $completedMilestones }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-slate-500 font-semibold block">Total Tasks</span>
                    <span class="text-lg font-bold text-slate-900">{{ $totalTasks }}</span>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <span class="text-blue-800 font-semibold block">Completed Tasks</span>
                    <span class="text-lg font-bold text-blue-700">{{ $completedTasks }}</span>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-slate-500 font-semibold block">Open Tasks</span>
                    <span class="text-lg font-bold text-slate-900">{{ $openTasks }}</span>
                </div>
                <div class="p-3 {{ $overdueTasks > 0 ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-slate-50 border-slate-200 text-slate-700' }} rounded-lg border">
                    <span class="font-semibold block">Overdue Tasks</span>
                    <span class="text-lg font-bold {{ $overdueTasks > 0 ? 'text-rose-700' : 'text-slate-900' }}">{{ $overdueTasks }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Workspace Sub-Sections (Requirements, Milestones, Tasks, Activity Log) -->
    <div class="space-y-8 pt-4">

        <!-- 1. Requirements Section -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6" id="requirements-section">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Project Requirements ({{ $project->requirements->count() }})
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Manage functional specifications and scope requirements.</p>
                </div>
                
                <button type="button" onclick="document.getElementById('add-requirement-form').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    + Add Requirement
                </button>
            </div>

            <!-- Create Requirement Form (Collapsible) -->
            <div id="add-requirement-form" class="hidden p-4 bg-slate-50 rounded-xl border border-slate-200">
                <h4 class="text-xs font-bold uppercase text-slate-700 mb-3">Add New Requirement</h4>
                <form action="{{ route('admin.projects.requirements.store', $project->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Requirement Title *</label>
                            <input type="text" name="title" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Priority *</label>
                            <select name="priority" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                                @foreach($requirementPriorities as $rp)
                                    <option value="{{ $rp->value }}" {{ $rp->value === 'medium' ? 'selected' : '' }}>{{ $rp->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Initial Status *</label>
                            <select name="status" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                                @foreach($requirementStatuses as $rs)
                                    <option value="{{ $rs->value }}" {{ $rs->value === 'submitted' ? 'selected' : '' }}>{{ $rs->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Submitted By</label>
                            <select name="submitted_by_id" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                                <option value="">Current User ({{ auth()->user()->name }})</option>
                                @foreach($clients as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} (Client)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Description / Details</label>
                            <textarea name="description" rows="2" class="w-full text-xs p-2.5 border border-slate-300 rounded-lg" placeholder="Functional requirements, acceptance criteria..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('add-requirement-form').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 bg-white border border-slate-200 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Save Requirement</button>
                    </div>
                </form>
            </div>

            <!-- Requirements List -->
            <div class="space-y-3">
                @forelse($project->requirements as $req)
                    <div class="p-4 rounded-xl border border-slate-200 hover:border-slate-300 transition-colors bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-xs">{{ $req->title }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase border
                                    {{ $req->priority->value === 'urgent' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                    {{ $req->priority->value === 'high' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                    {{ $req->priority->value === 'medium' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                    {{ $req->priority->value === 'low' ? 'bg-slate-50 text-slate-700 border-slate-200' : '' }}
                                ">
                                    {{ $req->priority->label() }}
                                </span>
                            </div>
                            @if($req->description)
                                <p class="text-xs text-slate-600 leading-relaxed">{{ $req->description }}</p>
                            @endif
                            <div class="text-[10px] text-slate-400">
                                Submitted by: {{ $req->submittedBy->name ?? 'Client/System' }} &bull; {{ $req->created_at->format('M d, Y') }}
                            </div>
                        </div>

                        <!-- Status Transition Form -->
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.projects.requirements.status', [$project->id, $req->id]) }}" method="POST" class="flex items-center gap-1.5">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs font-bold px-2.5 py-1 rounded-lg border border-slate-300 bg-slate-50">
                                    @foreach($requirementStatuses as $rs)
                                        <option value="{{ $rs->value }}" {{ $req->status->value === $rs->value ? 'selected' : '' }}>
                                            {{ $rs->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-xs">
                        No project requirements defined yet. Click "+ Add Requirement" to create the first specification.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 2. Milestones Section -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6" id="milestones-section">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        Project Delivery Milestones ({{ $project->milestones->count() }})
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Sequential delivery checkpoints and financial milestone tracking.</p>
                </div>
                
                <button type="button" onclick="document.getElementById('add-milestone-form').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    + Add Milestone
                </button>
            </div>

            <!-- Create Milestone Form (Collapsible) -->
            <div id="add-milestone-form" class="hidden p-4 bg-slate-50 rounded-xl border border-slate-200">
                <h4 class="text-xs font-bold uppercase text-slate-700 mb-3">Add New Milestone</h4>
                <form action="{{ route('admin.projects.milestones.store', $project->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Milestone Title *</label>
                            <input type="text" name="title" required placeholder="e.g. Phase 1: MVP Architecture & Auth" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sequence Order</label>
                            <input type="number" min="1" name="sequence_order" value="{{ ($project->milestones->max('sequence_order') ?? 0) + 1 }}" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                            <select name="status" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                                @foreach($milestoneStatuses as $ms)
                                    <option value="{{ $ms->value }}" {{ $ms->value === 'pending' ? 'selected' : '' }}>{{ $ms->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Milestone Amount (₹)</label>
                            <input type="number" step="0.01" min="0" name="amount" value="0.00" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div class="sm:col-span-4">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Milestone Scope / Notes</label>
                            <textarea name="description" rows="2" class="w-full text-xs p-2.5 border border-slate-300 rounded-lg" placeholder="Deliverables included in this milestone..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('add-milestone-form').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 bg-white border border-slate-200 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Save Milestone</button>
                    </div>
                </form>
            </div>

            <!-- Milestones Ordered Stream -->
            <div class="space-y-4">
                @forelse($project->milestones as $ms)
                    <div class="p-4 rounded-xl border border-slate-200 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-7 h-7 rounded-full bg-slate-900 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">
                                {{ $ms->sequence_order }}
                            </div>
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-bold text-slate-900 text-xs">{{ $ms->title }}</span>
                                    <span class="text-xs font-semibold text-emerald-700">₹{{ number_format($ms->amount, 2) }}</span>
                                </div>
                                @if($ms->description)
                                    <p class="text-xs text-slate-600 leading-relaxed">{{ $ms->description }}</p>
                                @endif
                                <div class="text-[10px] text-slate-400">
                                    Due: {{ $ms->due_date ? $ms->due_date->format('M d, Y') : 'N/A' }}
                                    @if($ms->completed_date)
                                        &bull; Completed on {{ $ms->completed_date->format('M d, Y') }}
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Status Toggle Form -->
                        <div class="flex items-center gap-2">
                            <form action="{{ route('admin.projects.milestones.status', [$project->id, $ms->id]) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <select name="status" onchange="this.form.submit()" class="text-xs font-bold px-2.5 py-1 rounded-lg border border-slate-300 bg-slate-50">
                                    @foreach($milestoneStatuses as $mst)
                                        <option value="{{ $mst->value }}" {{ $ms->status->value === $mst->value ? 'selected' : '' }}>
                                            {{ $mst->label() }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-center text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-xs">
                        No delivery milestones configured for this project yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- 3. Tasks Management Section -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6" id="tasks-section">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
                <div>
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Project Tasks ({{ $project->tasks->count() }})
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">Operational tasks, staff assignments, and milestone links.</p>
                </div>
                
                <button type="button" onclick="document.getElementById('add-task-form').classList.toggle('hidden')" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-lg shadow-sm transition-colors">
                    + Add Task
                </button>
            </div>

            <!-- Create Task Form (Collapsible) -->
            <div id="add-task-form" class="hidden p-4 bg-slate-50 rounded-xl border border-slate-200">
                <h4 class="text-xs font-bold uppercase text-slate-700 mb-3">Add New Task</h4>
                <form action="{{ route('admin.projects.tasks.store', $project->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Task Title *</label>
                            <input type="text" name="title" required placeholder="e.g. Implement OAuth login controller" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Priority *</label>
                            <select name="priority" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                                @foreach($taskPriorities as $tp)
                                    <option value="{{ $tp->value }}" {{ $tp->value === 'medium' ? 'selected' : '' }}>{{ $tp->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                            <select name="status" required class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                                @foreach($taskStatuses as $ts)
                                    <option value="{{ $ts->value }}" {{ $ts->value === 'todo' ? 'selected' : '' }}>{{ $ts->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Assigned Staff</label>
                            <select name="assigned_user_id" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                                <option value="">Unassigned</option>
                                @foreach($staffUsers as $su)
                                    <option value="{{ $su->id }}">{{ $su->name }} ({{ $su->role->value }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Linked Milestone</label>
                            <select name="milestone_id" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                                <option value="">No Milestone Link</option>
                                @foreach($project->milestones as $ms)
                                    <option value="{{ $ms->id }}">{{ $ms->sequence_order }}. {{ $ms->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Due Date</label>
                            <input type="date" name="due_date" class="w-full text-xs px-3 py-2 border border-slate-300 rounded-lg">
                        </div>
                        <div class="sm:col-span-3">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Task Description</label>
                            <textarea name="description" rows="2" class="w-full text-xs p-2.5 border border-slate-300 rounded-lg" placeholder="Technical instructions, requirements..."></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" onclick="document.getElementById('add-task-form').classList.add('hidden')" class="px-3 py-1.5 text-xs text-slate-600 bg-white border border-slate-200 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg">Save Task</button>
                    </div>
                </form>
            </div>

            <!-- Tasks Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-500 uppercase">
                            <th class="px-4 py-3">Task Title</th>
                            <th class="px-4 py-3">Milestone Link</th>
                            <th class="px-4 py-3">Assigned Staff</th>
                            <th class="px-4 py-3">Priority</th>
                            <th class="px-4 py-3">Due Date</th>
                            <th class="px-4 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 text-xs">
                        @forelse($project->tasks as $tk)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-4 py-3 font-semibold text-slate-900">
                                    <div>{{ $tk->title }}</div>
                                    @if($tk->description)
                                        <div class="text-[11px] text-slate-500 line-clamp-1 font-normal">{{ $tk->description }}</div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-slate-600">
                                    @if($tk->milestone)
                                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-800 px-2 py-0.5 rounded text-[10px] font-semibold">
                                            #{{ $tk->milestone->sequence_order }} {{ $tk->milestone->title }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">&mdash;</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-slate-800">
                                    @if($tk->assignedUser)
                                        <div class="flex items-center gap-1.5">
                                            <div class="w-5 h-5 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-[10px]">
                                                {{ strtoupper(substr($tk->assignedUser->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $tk->assignedUser->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-slate-400 italic">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase border
                                        {{ $tk->priority->value === 'urgent' ? 'bg-rose-50 text-rose-700 border-rose-200' : '' }}
                                        {{ $tk->priority->value === 'high' ? 'bg-amber-50 text-amber-700 border-amber-200' : '' }}
                                        {{ $tk->priority->value === 'medium' ? 'bg-blue-50 text-blue-700 border-blue-200' : '' }}
                                        {{ $tk->priority->value === 'low' ? 'bg-slate-50 text-slate-700 border-slate-200' : '' }}
                                    ">
                                        {{ $tk->priority->label() }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-mono text-slate-600">
                                    @if($tk->due_date)
                                        <span class="{{ $tk->due_date->isPast() && $tk->status->value !== 'completed' ? 'text-rose-600 font-bold' : '' }}">
                                            {{ $tk->due_date->format('M d, Y') }}
                                        </span>
                                    @else
                                        <span class="text-slate-400">&mdash;</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <form action="{{ route('admin.projects.tasks.status', [$project->id, $tk->id]) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()" class="text-xs font-bold px-2 py-1 rounded-lg border border-slate-300 bg-slate-50">
                                            @foreach($taskStatuses as $tst)
                                                <option value="{{ $tst->value }}" {{ $tk->status->value === $tst->value ? 'selected' : '' }}>
                                                    {{ $tst->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-500 italic text-xs">
                                    No tasks registered for this project yet. Click "+ Add Task" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 4. Activity Audit Timeline Section -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-200 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Project Delivery Audit Log
            </h3>

            <div class="space-y-3">
                @forelse($activityLogs as $log)
                    <div class="flex gap-3 text-xs border-l-2 border-blue-500 pl-3 py-1.5">
                        <div>
                            <p class="font-bold text-slate-900">{{ $log->description ?? $log->action }}</p>
                            <p class="text-slate-500 mt-0.5">
                                Actor: <span class="font-semibold text-slate-700">{{ $log->actor->name ?? 'System' }}</span> &bull; {{ $log->created_at->format('M d, Y H:i A') }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic">No activity recorded for this project yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

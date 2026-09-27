<?php

namespace App\Http\Controllers\Client;

use App\Enums\MilestoneStatus;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display the authenticated client's project directory.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = $user->projects()->with(['service', 'industry', 'milestones']);

        // Search Filter
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('reference_number', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $projects = $query->latest()->paginate(10)->withQueryString();

        $statuses = ProjectStatus::cases();

        return view('client.projects.index', compact('projects', 'statuses'));
    }

    /**
     * Display the authenticated client's project workspace.
     */
    public function show(Request $request, Project $project): View
    {
        Gate::authorize('view', $project);

        $project->load([
            'service',
            'industry',
            'milestones' => function ($q) {
                $q->orderBy('sequence_order');
            },
            'milestones.tasks',
            'requirements',
            'tasks',
        ]);

        // Calculate progress percentage based on completed milestones
        $totalMilestones = $project->milestones->count();
        $completedMilestones = $project->milestones->filter(function ($milestone) {
            return $milestone->status === MilestoneStatus::COMPLETED
                || (is_string($milestone->status) && $milestone->status === 'completed');
        })->count();

        if ($totalMilestones > 0) {
            $progressPercent = (int) round(($completedMilestones / $totalMilestones) * 100);
        } else {
            $progressPercent = $project->status === ProjectStatus::COMPLETED ? 100 : 0;
        }

        return view('client.projects.show', compact('project', 'progressPercent'));
    }
}

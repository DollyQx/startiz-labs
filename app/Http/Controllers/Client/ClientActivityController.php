<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientActivityController extends Controller
{
    /**
     * Display a client-safe overall activity timeline across all owned projects.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = ActivityLog::forClient($user->id)
            ->clientVisible()
            ->with(['actor', 'project'])
            ->orderByDesc('created_at');

        if ($request->filled('project_id')) {
            $projectId = (int) $request->input('project_id');
            // Strict IDOR Check: Ensure project belongs to authenticated client
            $ownedProject = $user->projects()->where('id', $projectId)->firstOrFail();
            $query->where('project_id', $ownedProject->id);
        }

        $activities = $query->paginate(15)->withQueryString();
        $projects = $user->projects()->orderBy('title')->get();

        return view('client.activity.index', [
            'activities' => $activities,
            'projects' => $projects,
            'selectedProjectId' => $request->input('project_id'),
        ]);
    }

    /**
     * Display a client-safe activity timeline for a specific project.
     */
    public function projectTimeline(Request $request, Project $project): View
    {
        Gate::authorize('view', $project);

        // Strict Server-Side Authorization & Isolation Check
        if ((int) $project->client_id !== (int) $request->user()->id) {
            abort(403, 'Unauthorized access to project activity history.');
        }

        $activities = ActivityLog::forProject($project->id)
            ->clientVisible()
            ->with(['actor'])
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('client.projects.activity', [
            'project' => $project,
            'activities' => $activities,
        ]);
    }
}

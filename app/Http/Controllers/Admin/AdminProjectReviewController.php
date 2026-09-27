<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectSignOffStatus;
use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectReviewIteration;
use App\Models\ProjectSignOff;
use App\Services\ActivityLogger;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminProjectReviewController extends Controller
{
    /**
     * Display the admin project review and sign-off workspace.
     */
    public function show(Project $project): View
    {
        $project->load([
            'client',
            'signOff.iterations',
            'milestones',
            'documents',
            'requirements',
            'tasks',
        ]);

        return view('admin.projects.review', [
            'project' => $project,
            'signOff' => $project->signOff,
            'iterations' => $project->signOff?->iterations ?? collect(),
        ]);
    }

    /**
     * Request client review for the project.
     */
    public function requestReview(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Move project lifecycle to CLIENT_REVIEW
        $project->update([
            'status' => ProjectStatus::CLIENT_REVIEW,
        ]);

        // Find or create ProjectSignOff
        $signOff = ProjectSignOff::firstOrCreate([
            'project_id' => $project->id,
        ], [
            'client_id' => $project->client_id,
            'status' => ProjectSignOffStatus::PENDING_REVIEW,
            'requested_by_id' => auth()->id(),
        ]);

        $signOff->update([
            'status' => ProjectSignOffStatus::PENDING_REVIEW,
            'requested_by_id' => auth()->id(),
        ]);

        // Calculate iteration number
        $nextIteration = ($signOff->iterations()->max('iteration_number') ?? 0) + 1;

        ProjectReviewIteration::create([
            'project_sign_off_id' => $signOff->id,
            'project_id' => $project->id,
            'iteration_number' => $nextIteration,
            'status' => ProjectSignOffStatus::PENDING_REVIEW,
            'review_notes' => $request->input('review_notes'),
            'responded_at' => now(),
        ]);

        // Log Activity
        ActivityLogger::log(
            'project.review_requested',
            $project,
            "Requested client review for project {$project->reference_number} (Iteration #{$nextIteration})",
            [
                'iteration_number' => $nextIteration,
                'review_notes' => $request->input('review_notes'),
            ]
        );

        // Notify Client
        if ($project->client) {
            NotificationService::notifyUser(
                $project->client,
                'project',
                'Client Review Requested',
                "Startiz Labs team has requested your review on project {$project->reference_number}.",
                route('client.projects.review', $project->id)
            );
        }

        return redirect()->route('admin.projects.review', $project->id)
            ->with('status', 'Client review requested successfully.');
    }

    /**
     * Mark final delivery for the project.
     */
    public function markFinalDelivery(Request $request, Project $project): RedirectResponse
    {
        $request->validate([
            'final_delivery_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $signOff = $project->signOff;

        if (! $signOff || ! $signOff->isApproved()) {
            return back()->withErrors(['error' => 'Cannot mark final delivery: project has not received client approval yet.']);
        }

        $signOff->update([
            'final_delivery_at' => now(),
            'final_delivery_by_id' => auth()->id(),
            'final_delivery_notes' => $request->input('final_delivery_notes'),
        ]);

        $project->update([
            'status' => ProjectStatus::COMPLETED,
            'actual_completion_date' => now(),
        ]);

        ActivityLogger::log(
            'project.final_delivery_recorded',
            $project,
            "Recorded final delivery and completed project {$project->reference_number}",
            [
                'final_delivery_notes' => $request->input('final_delivery_notes'),
            ]
        );

        if ($project->client) {
            NotificationService::notifyUser(
                $project->client,
                'project',
                'Project Final Delivery Completed',
                "Project {$project->reference_number} has been marked as delivered and completed. Thank you for working with Startiz Labs!",
                route('client.projects.show', $project->id)
            );
        }

        return redirect()->route('admin.projects.review', $project->id)
            ->with('status', 'Final delivery recorded and project marked as completed.');
    }
}

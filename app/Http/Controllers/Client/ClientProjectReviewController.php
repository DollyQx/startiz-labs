<?php

namespace App\Http\Controllers\Client;

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

class ClientProjectReviewController extends Controller
{
    /**
     * Display the client project review workspace.
     */
    public function show(Request $request, Project $project): View
    {
        // Enforce strict client project isolation
        if ((int) $project->client_id !== (int) $request->user()->id) {
            abort(403, 'Unauthorized access to project review.');
        }

        $project->load([
            'signOff.iterations',
            'milestones',
            'documents',
            'requirements',
            'tasks',
        ]);

        return view('client.projects.review', [
            'project' => $project,
            'signOff' => $project->signOff,
            'iterations' => $project->signOff?->iterations ?? collect(),
        ]);
    }

    /**
     * Submit feedback / revision request for project review.
     */
    public function submitFeedback(Request $request, Project $project): RedirectResponse
    {
        // Enforce strict client project isolation
        if ((int) $project->client_id !== (int) $request->user()->id) {
            abort(403, 'Unauthorized access to project review.');
        }

        $request->validate([
            'feedback' => ['required', 'string', 'min:3', 'max:5000'],
        ]);

        $signOff = $project->signOff;

        if (! $signOff || $signOff->isApproved()) {
            return back()->withErrors(['feedback' => 'Review is not currently open for feedback or has already been approved.']);
        }

        // Update SignOff status
        $signOff->update([
            'status' => ProjectSignOffStatus::FEEDBACK_REQUIRED,
        ]);

        // Get or create current iteration
        $currentIteration = $signOff->currentIteration;

        if (! $currentIteration) {
            $currentIteration = ProjectReviewIteration::create([
                'project_sign_off_id' => $signOff->id,
                'project_id' => $project->id,
                'iteration_number' => 1,
                'status' => ProjectSignOffStatus::FEEDBACK_REQUIRED,
            ]);
        }

        $currentIteration->update([
            'status' => ProjectSignOffStatus::FEEDBACK_REQUIRED,
            'client_feedback' => $request->input('feedback'),
            'submitted_at' => now(),
        ]);

        // Log Activity
        ActivityLogger::log(
            'project.feedback_submitted',
            $project,
            "Client submitted review feedback for project {$project->reference_number}",
            [
                'iteration_number' => $currentIteration->iteration_number,
                'feedback' => $request->input('feedback'),
            ]
        );

        // Notify Staff
        NotificationService::notifyRoles(
            ['admin', 'super_admin', 'project_manager'],
            'project',
            'Client Review Feedback Submitted',
            "Client {$request->user()->name} submitted feedback for project {$project->reference_number}.",
            route('admin.projects.review', $project->id)
        );

        return redirect()->route('client.projects.review', $project->id)
            ->with('status', 'Your review feedback has been submitted to the project team.');
    }

    /**
     * Approve and grant final project sign-off.
     */
    public function approveSignOff(Request $request, Project $project): RedirectResponse
    {
        // Enforce strict client project isolation
        if ((int) $project->client_id !== (int) $request->user()->id) {
            abort(403, 'Unauthorized access to project review.');
        }

        $signOff = $project->signOff;

        if (! $signOff) {
            return back()->withErrors(['approval' => 'No active review request found for this project.']);
        }

        // Idempotency check: if already approved, do not re-process
        if ($signOff->isApproved()) {
            return redirect()->route('client.projects.review', $project->id)
                ->with('status', 'This project has already been approved and signed off.');
        }

        // Server-side validation: must be in PENDING_REVIEW or FEEDBACK_REQUIRED state
        if (! in_array($signOff->status, [ProjectSignOffStatus::PENDING_REVIEW, ProjectSignOffStatus::FEEDBACK_REQUIRED], true)) {
            return back()->withErrors(['approval' => 'The project is not currently eligible for sign-off approval.']);
        }

        // Mark sign-off approved
        $signOff->update([
            'status' => ProjectSignOffStatus::APPROVED,
            'accepted_at' => now(),
            'accepted_by_id' => $request->user()->id,
        ]);

        // Update current iteration
        if ($signOff->currentIteration) {
            $signOff->currentIteration->update([
                'status' => ProjectSignOffStatus::APPROVED,
            ]);
        }

        // Log Activity
        ActivityLogger::log(
            'project.client_approved',
            $project,
            "Client {$request->user()->name} granted final approval & sign-off for project {$project->reference_number}",
            [
                'accepted_at' => now()->toIso8601String(),
                'accepted_by_id' => $request->user()->id,
            ]
        );

        // Notify Staff
        NotificationService::notifyRoles(
            ['admin', 'super_admin', 'project_manager'],
            'project',
            'Project Final Acceptance Granted',
            "Client {$request->user()->name} has granted final approval and sign-off for project {$project->reference_number}.",
            route('admin.projects.review', $project->id)
        );

        return redirect()->route('client.projects.review', $project->id)
            ->with('status', 'Thank you! Final approval and sign-off has been recorded.');
    }
}

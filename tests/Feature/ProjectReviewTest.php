<?php

namespace Tests\Feature;

use App\Enums\ProjectSignOffStatus;
use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Project;
use App\Models\ProjectReviewIteration;
use App\Models\ProjectSignOff;
use App\Models\User;
use App\Notifications\GenericDatabaseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProjectReviewTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $clientA;
    protected User $clientB;
    protected Project $projectA;
    protected Project $projectB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->clientA = User::factory()->create([
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->clientB = User::factory()->create([
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Client A Web System',
            'status' => ProjectStatus::TESTING,
        ]);

        $this->projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Client B Portal',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        Notification::fake();
    }

    public function test_authorized_staff_can_request_client_review()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.review.request', $this->projectA), [
                'review_notes' => 'Please test the auth flow and checkout page.',
            ]);

        $response->assertRedirect(route('admin.projects.review', $this->projectA));

        $this->projectA->refresh();
        $this->assertEquals(ProjectStatus::CLIENT_REVIEW, $this->projectA->status);

        $signOff = ProjectSignOff::where('project_id', $this->projectA->id)->firstOrFail();
        $this->assertEquals(ProjectSignOffStatus::PENDING_REVIEW, $signOff->status);

        $this->assertDatabaseHas('project_review_iterations', [
            'project_sign_off_id' => $signOff->id,
            'iteration_number' => 1,
            'review_notes' => 'Please test the auth flow and checkout page.',
        ]);

        // Verify notification sent to client
        Notification::assertSentTo(
            [$this->clientA],
            GenericDatabaseNotification::class,
            function ($notification) {
                return $notification->category === 'project' &&
                       str_contains($notification->title, 'Client Review Requested');
            }
        );

        // Verify activity logged
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'project.review_requested',
            'project_id' => $this->projectA->id,
        ]);
    }

    public function test_client_can_view_review_page_and_submit_structured_feedback()
    {
        // Admin requests review first
        $this->actingAs($this->admin)
            ->post(route('admin.projects.review.request', $this->projectA), [
                'review_notes' => 'Review iteration #1',
            ]);

        // Client A views review page
        $viewResponse = $this->actingAs($this->clientA)
            ->get(route('client.projects.review', $this->projectA));

        $viewResponse->assertOk();
        $viewResponse->assertViewIs('client.projects.review');

        // Client A submits feedback
        $feedbackResponse = $this->actingAs($this->clientA)
            ->post(route('client.projects.review.feedback', $this->projectA), [
                'feedback' => 'Homepage header spacing needs to be reduced.',
            ]);

        $feedbackResponse->assertRedirect(route('client.projects.review', $this->projectA));

        $signOff = ProjectSignOff::where('project_id', $this->projectA->id)->firstOrFail();
        $this->assertEquals(ProjectSignOffStatus::FEEDBACK_REQUIRED, $signOff->status);

        $this->assertDatabaseHas('project_review_iterations', [
            'project_sign_off_id' => $signOff->id,
            'client_feedback' => 'Homepage header spacing needs to be reduced.',
        ]);

        // Verify notification sent to staff
        Notification::assertSentTo(
            [$this->admin],
            GenericDatabaseNotification::class,
            function ($notification) {
                return $notification->category === 'project' &&
                       str_contains($notification->title, 'Feedback Submitted');
            }
        );
    }

    public function test_wrong_client_cannot_access_or_submit_feedback_for_other_clients_project()
    {
        // Client A trying to access Client B's project review
        $viewResponse = $this->actingAs($this->clientA)
            ->get(route('client.projects.review', $this->projectB));

        $viewResponse->assertForbidden();

        // Client A trying to post feedback to Client B's project review
        $postResponse = $this->actingAs($this->clientA)
            ->post(route('client.projects.review.feedback', $this->projectB), [
                'feedback' => 'Malicious feedback attempt',
            ]);

        $postResponse->assertForbidden();

        $this->assertDatabaseMissing('project_review_iterations', [
            'project_id' => $this->projectB->id,
            'client_feedback' => 'Malicious feedback attempt',
        ]);
    }

    public function test_client_can_grant_final_approval_and_sign_off()
    {
        // Admin requests review first
        $this->actingAs($this->admin)
            ->post(route('admin.projects.review.request', $this->projectA), [
                'review_notes' => 'Ready for final review.',
            ]);

        // Client A approves
        $approvalResponse = $this->actingAs($this->clientA)
            ->post(route('client.projects.review.approve', $this->projectA));

        $approvalResponse->assertRedirect(route('client.projects.review', $this->projectA));

        $signOff = ProjectSignOff::where('project_id', $this->projectA->id)->firstOrFail();
        $this->assertEquals(ProjectSignOffStatus::APPROVED, $signOff->status);
        $this->assertNotNull($signOff->accepted_at);
        $this->assertEquals($this->clientA->id, $signOff->accepted_by_id);

        // Verify notification sent to staff
        Notification::assertSentTo(
            [$this->admin],
            GenericDatabaseNotification::class,
            function ($notification) {
                return $notification->category === 'project' &&
                       str_contains($notification->title, 'Final Acceptance Granted');
            }
        );

        // Verify activity logged
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'project.client_approved',
            'project_id' => $this->projectA->id,
        ]);
    }

    public function test_client_cannot_approve_unsubmitted_project()
    {
        // Project B has no review requested yet
        $response = $this->actingAs($this->clientB)
            ->post(route('client.projects.review.approve', $this->projectB));

        $response->assertSessionHasErrors(['approval']);
    }

    public function test_approval_cannot_be_duplicated_idempotency()
    {
        // Admin requests review
        $this->actingAs($this->admin)
            ->post(route('admin.projects.review.request', $this->projectA));

        // First approval
        $this->actingAs($this->clientA)
            ->post(route('client.projects.review.approve', $this->projectA));

        $signOff = ProjectSignOff::where('project_id', $this->projectA->id)->firstOrFail();
        $acceptedAt = $signOff->accepted_at;

        // Second approval attempt
        $secondResponse = $this->actingAs($this->clientA)
            ->post(route('client.projects.review.approve', $this->projectA));

        $secondResponse->assertRedirect(route('client.projects.review', $this->projectA));
        $this->assertEquals($acceptedAt->toDateTimeString(), $signOff->fresh()->accepted_at->toDateTimeString());
    }

    public function test_staff_can_mark_final_delivery_after_client_approval()
    {
        // Admin requests review and Client approves
        $this->actingAs($this->admin)->post(route('admin.projects.review.request', $this->projectA));
        $this->actingAs($this->clientA)->post(route('client.projects.review.approve', $this->projectA));

        // Admin marks final delivery
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.review.deliver', $this->projectA), [
                'final_delivery_notes' => 'Deployed to production. Credentials sent via secure note.',
            ]);

        $response->assertRedirect(route('admin.projects.review', $this->projectA));

        $this->projectA->refresh();
        $this->assertEquals(ProjectStatus::COMPLETED, $this->projectA->status);

        $signOff = ProjectSignOff::where('project_id', $this->projectA->id)->firstOrFail();
        $this->assertNotNull($signOff->final_delivery_at);
        $this->assertEquals('Deployed to production. Credentials sent via secure note.', $signOff->final_delivery_notes);
    }

    public function test_staff_cannot_mark_final_delivery_without_client_approval()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.projects.review.deliver', $this->projectA), [
                'final_delivery_notes' => 'Attempting delivery before client sign-off',
            ]);

        $response->assertSessionHasErrors(['error']);
        $this->assertNotEquals(ProjectStatus::COMPLETED, $this->projectA->fresh()->status);
    }

    public function test_multiple_review_iterations_preserve_historical_feedback()
    {
        // Cycle 1: Request & Feedback
        $this->actingAs($this->admin)->post(route('admin.projects.review.request', $this->projectA), ['review_notes' => 'Cycle 1']);
        $this->actingAs($this->clientA)->post(route('client.projects.review.feedback', $this->projectA), ['feedback' => 'Fix logo position']);

        // Cycle 2: Staff re-requests review after revisions
        $this->actingAs($this->admin)->post(route('admin.projects.review.request', $this->projectA), ['review_notes' => 'Cycle 2: Logo fixed']);

        $signOff = ProjectSignOff::where('project_id', $this->projectA->id)->firstOrFail();
        $this->assertEquals(2, $signOff->iterations()->count());

        $this->assertDatabaseHas('project_review_iterations', [
            'project_sign_off_id' => $signOff->id,
            'iteration_number' => 1,
            'client_feedback' => 'Fix logo position',
        ]);

        $this->assertDatabaseHas('project_review_iterations', [
            'project_sign_off_id' => $signOff->id,
            'iteration_number' => 2,
            'review_notes' => 'Cycle 2: Logo fixed',
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\Project;
use App\Models\ProjectMessage;
use App\Models\User;
use App\Notifications\GenericDatabaseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectMessagingTest extends TestCase
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

        // Create Admin user
        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        // Create Client A
        $this->clientA = User::factory()->create([
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        // Create Client B
        $this->clientB = User::factory()->create([
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        // Create Project for Client A
        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Client A E-Commerce System',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        // Create Project for Client B
        $this->projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Client B Mobile App',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        Storage::fake('local');
        Notification::fake();
    }

    public function test_client_can_view_and_send_messages_on_owned_project()
    {
        $response = $this->actingAs($this->clientA)
            ->get(route('client.projects.messages', $this->projectA));

        $response->assertOk();
        $response->assertViewIs('client.projects.messages');

        // Send a message as Client A
        $postResponse = $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectA), [
                'message' => 'Hello Startiz Labs team, please review the designs.',
            ]);

        $postResponse->assertRedirect(route('client.projects.messages', $this->projectA));
        $postResponse->assertSessionHas('status');

        $this->assertDatabaseHas('project_messages', [
            'project_id' => $this->projectA->id,
            'sender_id' => $this->clientA->id,
            'message' => 'Hello Startiz Labs team, please review the designs.',
        ]);
    }

    public function test_client_cannot_access_or_send_messages_to_other_clients_project()
    {
        // Client A trying to access Client B's project messages
        $viewResponse = $this->actingAs($this->clientA)
            ->get(route('client.projects.messages', $this->projectB));

        $viewResponse->assertForbidden();

        // Client A trying to post to Client B's project messages
        $postResponse = $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectB), [
                'message' => 'Unauthorized message attempt',
            ]);

        $postResponse->assertForbidden();

        $this->assertDatabaseMissing('project_messages', [
            'project_id' => $this->projectB->id,
            'message' => 'Unauthorized message attempt',
        ]);
    }

    public function test_authorized_admin_can_view_and_send_messages_on_any_project()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.projects.messages', $this->projectA));

        $response->assertOk();
        $response->assertViewIs('admin.projects.messages');

        $postResponse = $this->actingAs($this->admin)
            ->post(route('admin.projects.messages.store', $this->projectA), [
                'message' => 'Admin reply: The homepage designs look great!',
            ]);

        $postResponse->assertRedirect(route('admin.projects.messages', $this->projectA));

        $this->assertDatabaseHas('project_messages', [
            'project_id' => $this->projectA->id,
            'sender_id' => $this->admin->id,
            'message' => 'Admin reply: The homepage designs look great!',
        ]);
    }

    public function test_empty_message_and_empty_attachment_are_rejected()
    {
        $response = $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectA), [
                'message' => '',
                'attachment' => null,
            ]);

        $response->assertSessionHasErrors(['message', 'attachment']);
    }

    public function test_executable_attachment_extension_is_rejected()
    {
        $phpFile = UploadedFile::fake()->create('malicious.php', 10, 'text/x-php');

        $response = $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectA), [
                'message' => 'Here is a script file',
                'attachment' => $phpFile,
            ]);

        $response->assertSessionHasErrors(['attachment']);
        $this->assertDatabaseMissing('project_messages', [
            'project_id' => $this->projectA->id,
            'original_filename' => 'malicious.php',
        ]);
    }

    public function test_valid_attachment_upload_and_authorized_private_download()
    {
        $pdfFile = UploadedFile::fake()->create('wireframes.pdf', 500, 'application/pdf');

        $postResponse = $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectA), [
                'message' => 'Attached project wireframes.',
                'attachment' => $pdfFile,
            ]);

        $postResponse->assertRedirect();

        $message = ProjectMessage::where('project_id', $this->projectA->id)->firstOrFail();
        $this->assertNotNull($message->attachment_path);
        $this->assertEquals('wireframes.pdf', $message->original_filename);

        Storage::disk('local')->assertExists($message->attachment_path);

        // Authorized download by client owner
        $downloadResponse = $this->actingAs($this->clientA)
            ->get(route('client.projects.messages.download', [$this->projectA->id, $message->id]));

        $downloadResponse->assertOk();

        // Unauthorized download by another client
        $unauthorizedDownload = $this->actingAs($this->clientB)
            ->get(route('client.projects.messages.download', [$this->projectA->id, $message->id]));

        $unauthorizedDownload->assertForbidden();
    }

    public function test_malicious_script_input_is_safely_escaped_in_view()
    {
        $xssMessage = '<script>alert("XSS")</script><b>Bold Text</b>';

        $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectA), [
                'message' => $xssMessage,
            ]);

        $viewResponse = $this->actingAs($this->clientA)
            ->get(route('client.projects.messages', $this->projectA));

        $viewResponse->assertOk();
        $viewResponse->assertSee(e($xssMessage), false);
        $viewResponse->assertDontSee('<script>alert("XSS")</script>', false);
    }

    public function test_per_user_message_read_state_tracking()
    {
        // Admin sends message
        $this->actingAs($this->admin)
            ->post(route('admin.projects.messages.store', $this->projectA), [
                'message' => 'Message for Client A',
            ]);

        $message = ProjectMessage::where('project_id', $this->projectA->id)->firstOrFail();

        // Admin sender has read it automatically
        $this->assertTrue($message->isReadBy($this->admin));
        // Client A has not read it yet
        $this->assertFalse($message->isReadBy($this->clientA));

        // Client A views the messages page
        $this->actingAs($this->clientA)
            ->get(route('client.projects.messages', $this->projectA));

        // Now Client A has read state recorded
        $this->assertTrue($message->fresh()->isReadBy($this->clientA));
    }

    public function test_client_message_triggers_staff_notification_without_duplicates()
    {
        $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectA), [
                'message' => 'Need an update on delivery',
            ]);

        Notification::assertSentTo(
            [$this->admin],
            GenericDatabaseNotification::class,
            function ($notification) {
                return $notification->category === 'project_message' &&
                       str_contains($notification->title, 'Client Message');
            }
        );
    }

    public function test_staff_message_triggers_client_notification()
    {
        $this->actingAs($this->admin)
            ->post(route('admin.projects.messages.store', $this->projectA), [
                'message' => 'Project update: Phase 1 complete.',
            ]);

        Notification::assertSentTo(
            [$this->clientA],
            GenericDatabaseNotification::class,
            function ($notification) {
                return $notification->category === 'project_message' &&
                       str_contains($notification->title, 'New Message');
            }
        );
    }

    public function test_activity_logging_records_message_and_attachment_events()
    {
        $file = UploadedFile::fake()->create('spec.pdf', 200, 'application/pdf');

        $this->actingAs($this->clientA)
            ->post(route('client.projects.messages.store', $this->projectA), [
                'message' => 'Uploading final spec',
                'attachment' => $file,
            ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'project.message_sent',
            'project_id' => $this->projectA->id,
            'client_id' => $this->clientA->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'project.message_attachment_uploaded',
            'project_id' => $this->projectA->id,
        ]);
    }
}

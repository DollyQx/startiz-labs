<?php

namespace Tests\Feature\Admin;

use App\Enums\DocumentType;
use App\Enums\DocumentVisibility;
use App\Enums\ProjectStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\Document;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create([
            'name' => 'System Admin',
            'email' => 'admin@doc.test',
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->client = User::factory()->create([
            'name' => 'Apex Corp',
            'email' => 'apex@doc.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'title' => 'CRM Cloud Migration',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);
    }

    public function test_admin_can_view_documents_directory(): void
    {
        $doc = Document::create([
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::CONTRACT->value,
            'original_filename' => 'Service-Agreement.pdf',
            'storage_path' => 'documents/fake-hash-123.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 10240,
            'visibility' => DocumentVisibility::CLIENT->value,
            'description' => 'Signed master service agreement',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.documents.index'))
            ->assertStatus(200)
            ->assertSee('Service-Agreement.pdf')
            ->assertSee($doc->reference_number)
            ->assertSee('Apex Corp');
    }

    public function test_admin_can_upload_document_to_private_storage(): void
    {
        $file = UploadedFile::fake()->create('project_proposal_v1.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->client->id,
                'project_id' => $this->project->id,
                'document_type' => DocumentType::PROPOSAL->value,
                'visibility' => DocumentVisibility::CLIENT->value,
                'description' => 'Initial scope proposal for CRM migration',
                'file' => $file,
            ]);

        $response->assertRedirect();

        $document = Document::where('original_filename', 'project_proposal_v1.pdf')->first();
        $this->assertNotNull($document);
        $this->assertEquals($this->client->id, $document->client_id);
        $this->assertEquals($this->project->id, $document->project_id);
        $this->assertEquals(DocumentType::PROPOSAL, $document->document_type);
        $this->assertEquals(DocumentVisibility::CLIENT, $document->visibility);

        // Verify private storage and randomized path (not original filename path directly)
        $this->assertNotEquals('documents/project_proposal_v1.pdf', $document->storage_path);
        Storage::disk('local')->assertExists($document->storage_path);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'document_uploaded',
            'subject_id' => $document->id,
        ]);
    }

    public function test_invalid_file_extension_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('malicious_script.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->client->id,
                'project_id' => $this->project->id,
                'document_type' => DocumentType::OTHER->value,
                'visibility' => DocumentVisibility::PRIVATE->value,
                'file' => $file,
            ]);

        $response->assertSessionHasErrors(['file']);
    }

    public function test_mismatched_client_and_project_is_rejected(): void
    {
        $otherClient = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        // Submitting project belonging to Client A with Client B ID
        $response = $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $otherClient->id,
                'project_id' => $this->project->id, // Belongs to $this->client
                'document_type' => DocumentType::OTHER->value,
                'visibility' => DocumentVisibility::PRIVATE->value,
                'file' => $file,
            ]);

        $response->assertSessionHasErrors(['project_id']);
    }

    public function test_admin_can_download_document(): void
    {
        $file = UploadedFile::fake()->create('architecture-diagram.png', 200, 'image/png');

        $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->client->id,
                'project_id' => $this->project->id,
                'document_type' => DocumentType::DELIVERABLE->value,
                'visibility' => DocumentVisibility::CLIENT->value,
                'file' => $file,
            ]);

        $document = Document::where('original_filename', 'architecture-diagram.png')->firstOrFail();

        $response = $this->actingAs($this->admin)
            ->get(route('admin.documents.download', $document));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'document_downloaded',
            'subject_id' => $document->id,
        ]);
    }

    public function test_admin_can_update_document_visibility(): void
    {
        $document = Document::create([
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::CONTRACT->value,
            'original_filename' => 'Internal-Notes.pdf',
            'storage_path' => 'documents/fake-1.pdf',
            'visibility' => DocumentVisibility::PRIVATE->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.documents.visibility', $document), [
                'visibility' => DocumentVisibility::CLIENT->value,
            ]);

        $response->assertRedirect();
        $this->assertEquals(DocumentVisibility::CLIENT, $document->fresh()->visibility);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'document_visibility_changed',
            'subject_id' => $document->id,
        ]);
    }

    public function test_admin_can_delete_document_and_purges_physical_file(): void
    {
        $file = UploadedFile::fake()->create('obsolete-spec.pdf', 100, 'application/pdf');

        $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->client->id,
                'project_id' => $this->project->id,
                'document_type' => DocumentType::REQUIREMENT->value,
                'visibility' => DocumentVisibility::PRIVATE->value,
                'file' => $file,
            ]);

        $document = Document::where('original_filename', 'obsolete-spec.pdf')->firstOrFail();
        $storagePath = $document->storage_path;

        Storage::disk('local')->assertExists($storagePath);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.documents.destroy', $document));

        $response->assertRedirect(route('admin.documents.index'));

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
        Storage::disk('local')->assertMissing($storagePath);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'document_deleted',
            'subject_id' => $document->id,
        ]);
    }

    public function test_unauthorized_role_cannot_upload_admin_documents(): void
    {
        $file = UploadedFile::fake()->create('unauthorized.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->client)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->client->id,
                'document_type' => DocumentType::OTHER->value,
                'visibility' => DocumentVisibility::CLIENT->value,
                'file' => $file,
            ]);

        $response->assertStatus(403);
    }
}

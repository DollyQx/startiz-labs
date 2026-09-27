<?php

namespace Tests\Feature\Client;

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

class DocumentAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $clientA;
    private User $clientB;
    private Project $projectA;
    private Project $projectB;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->clientA = User::factory()->create([
            'name' => 'Client Alpha',
            'email' => 'alpha@doc.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->clientB = User::factory()->create([
            'name' => 'Client Beta',
            'email' => 'beta@doc.test',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->projectA = Project::factory()->create([
            'client_id' => $this->clientA->id,
            'title' => 'Alpha Web App',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);

        $this->projectB = Project::factory()->create([
            'client_id' => $this->clientB->id,
            'title' => 'Beta Mobile App',
            'status' => ProjectStatus::IN_PROGRESS,
        ]);
    }

    public function test_client_can_list_only_their_own_client_visible_documents(): void
    {
        // Client A - Visible
        $docAVisible = Document::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::CONTRACT->value,
            'original_filename' => 'Alpha-Contract.pdf',
            'storage_path' => 'documents/alpha-contract.pdf',
            'visibility' => DocumentVisibility::CLIENT->value,
        ]);

        // Client A - Private (Internal)
        $docAPrivate = Document::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::PROPOSAL->value,
            'original_filename' => 'Alpha-Internal-Estimate.pdf',
            'storage_path' => 'documents/alpha-private.pdf',
            'visibility' => DocumentVisibility::PRIVATE->value,
        ]);

        // Client B - Visible
        $docBVisible = Document::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::CONTRACT->value,
            'original_filename' => 'Beta-Contract.pdf',
            'storage_path' => 'documents/beta-contract.pdf',
            'visibility' => DocumentVisibility::CLIENT->value,
        ]);

        $response = $this->actingAs($this->clientA)
            ->get(route('client.documents.index'))
            ->assertStatus(200);

        $response->assertSee('Alpha-Contract.pdf');
        $response->assertDontSee('Alpha-Internal-Estimate.pdf');
        $response->assertDontSee('Beta-Contract.pdf');
    }

    public function test_client_can_view_detail_of_their_client_visible_document(): void
    {
        $doc = Document::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::DELIVERABLE->value,
            'original_filename' => 'Final-Design-Specs.pdf',
            'storage_path' => 'documents/specs.pdf',
            'visibility' => DocumentVisibility::CLIENT->value,
        ]);

        $this->actingAs($this->clientA)
            ->get(route('client.documents.show', $doc))
            ->assertStatus(200)
            ->assertSee('Final-Design-Specs.pdf')
            ->assertSee('Alpha Web App');
    }

    public function test_client_cannot_view_internal_private_document(): void
    {
        $internalDoc = Document::create([
            'client_id' => $this->clientA->id,
            'project_id' => $this->projectA->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::OTHER->value,
            'original_filename' => 'Audit-Report-Internal.pdf',
            'storage_path' => 'documents/audit.pdf',
            'visibility' => DocumentVisibility::PRIVATE->value,
        ]);

        $this->actingAs($this->clientA)
            ->get(route('client.documents.show', $internalDoc))
            ->assertStatus(403);
    }

    public function test_client_cannot_view_another_clients_document(): void
    {
        $docB = Document::create([
            'client_id' => $this->clientB->id,
            'project_id' => $this->projectB->id,
            'uploaded_by_id' => $this->admin->id,
            'document_type' => DocumentType::CONTRACT->value,
            'original_filename' => 'Beta-Agreement.pdf',
            'storage_path' => 'documents/beta.pdf',
            'visibility' => DocumentVisibility::CLIENT->value,
        ]);

        $this->actingAs($this->clientA)
            ->get(route('client.documents.show', $docB))
            ->assertStatus(403);
    }

    public function test_client_can_download_their_client_visible_document(): void
    {
        $file = UploadedFile::fake()->create('Alpha-Invoice.pdf', 150, 'application/pdf');

        $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->clientA->id,
                'project_id' => $this->projectA->id,
                'document_type' => DocumentType::INVOICE->value,
                'visibility' => DocumentVisibility::CLIENT->value,
                'file' => $file,
            ]);

        $doc = Document::where('original_filename', 'Alpha-Invoice.pdf')->firstOrFail();

        $response = $this->actingAs($this->clientA)
            ->get(route('client.documents.download', $doc));

        $response->assertStatus(200);
        $response->assertHeader('content-disposition');

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'document_downloaded',
            'subject_id' => $doc->id,
        ]);
    }

    public function test_client_cannot_download_another_clients_document(): void
    {
        $file = UploadedFile::fake()->create('Beta-Invoice.pdf', 150, 'application/pdf');

        $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->clientB->id,
                'project_id' => $this->projectB->id,
                'document_type' => DocumentType::INVOICE->value,
                'visibility' => DocumentVisibility::CLIENT->value,
                'file' => $file,
            ]);

        $docB = Document::where('original_filename', 'Beta-Invoice.pdf')->firstOrFail();

        // Client A trying to download Client B's document
        $this->actingAs($this->clientA)
            ->get(route('client.documents.download', $docB))
            ->assertStatus(403);
    }

    public function test_client_cannot_download_internal_private_document(): void
    {
        $file = UploadedFile::fake()->create('Private-Notes.pdf', 150, 'application/pdf');

        $this->actingAs($this->admin)
            ->post(route('admin.documents.store'), [
                'client_id' => $this->clientA->id,
                'project_id' => $this->projectA->id,
                'document_type' => DocumentType::OTHER->value,
                'visibility' => DocumentVisibility::PRIVATE->value,
                'file' => $file,
            ]);

        $privateDoc = Document::where('original_filename', 'Private-Notes.pdf')->firstOrFail();

        $this->actingAs($this->clientA)
            ->get(route('client.documents.download', $privateDoc))
            ->assertStatus(403);
    }
}

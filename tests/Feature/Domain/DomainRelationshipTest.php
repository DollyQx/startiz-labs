<?php

namespace Tests\Feature\Domain;

use App\Enums\MilestoneStatus;
use App\Enums\QuotationStatus;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ClientProfile;
use App\Models\Document;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectRequirement;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_client_profile_relationship(): void
    {
        $user = User::create([
            'name' => 'Client Test',
            'email' => 'clienttest@example.com',
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $profile = ClientProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Test Corp',
        ]);

        $this->assertNotNull($user->clientProfile);
        $this->assertEquals('Test Corp', $user->clientProfile->company_name);
        $this->assertEquals($user->id, $profile->user->id);
    }

    public function test_project_belongs_to_client_and_has_milestones_and_tasks(): void
    {
        $client = User::create([
            'name' => 'Project Client',
            'email' => 'projclient@example.com',
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Web App Development',
            'estimated_value' => 150000.00,
        ]);

        $milestone1 = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Phase 1: Architecture',
            'amount' => 50000.00,
            'sequence_order' => 1,
            'status' => MilestoneStatus::COMPLETED,
        ]);

        $milestone2 = ProjectMilestone::create([
            'project_id' => $project->id,
            'title' => 'Phase 2: Authentication',
            'amount' => 100000.00,
            'sequence_order' => 2,
            'status' => MilestoneStatus::IN_PROGRESS,
        ]);

        $task1 = Task::create([
            'project_id' => $project->id,
            'milestone_id' => $milestone2->id,
            'title' => 'Build login endpoints',
            'status' => TaskStatus::COMPLETED,
            'priority' => TaskPriority::HIGH,
        ]);

        $requirement = ProjectRequirement::create([
            'project_id' => $project->id,
            'title' => 'Support OAuth2 Login',
            'submitted_by_id' => $client->id,
        ]);

        $this->assertEquals($client->id, $project->client->id);
        $this->assertCount(2, $project->milestones);
        $this->assertCount(1, $project->tasks);
        $this->assertCount(1, $project->requirements);
        $this->assertEquals($project->id, $task1->project->id);
        $this->assertEquals($milestone2->id, $task1->milestone->id);
    }

    public function test_quotation_belongs_to_client_and_has_derived_item_totals(): void
    {
        $client = User::create([
            'name' => 'Quotation Client',
            'email' => 'quoclient@example.com',
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $service = Service::create([
            'name' => 'Custom Software',
            'slug' => 'custom-software',
        ]);

        $quotation = Quotation::create([
            'client_id' => $client->id,
            'issue_date' => now(),
            'valid_until' => now()->addDays(14),
            'status' => QuotationStatus::DRAFT,
        ]);

        $item1 = QuotationItem::create([
            'quotation_id' => $quotation->id,
            'service_id' => $service->id,
            'description' => 'Backend API setup',
            'quantity' => 1,
            'unit_price' => 50000.00,
            'discount' => 0.00,
            'tax' => 9000.00,
        ]);

        $item2 = QuotationItem::create([
            'quotation_id' => $quotation->id,
            'service_id' => $service->id,
            'description' => 'Frontend UI development',
            'quantity' => 2,
            'unit_price' => 25000.00,
            'discount' => 5000.00,
            'tax' => 8100.00,
        ]);

        $this->assertEquals(59000.00, $item1->line_total);
        $this->assertEquals(53100.00, $item2->line_total);

        $quotation->recalculateTotals();

        $this->assertEquals(100000.00, $quotation->subtotal);
        $this->assertEquals(5000.00, $quotation->discount);
        $this->assertEquals(17100.00, $quotation->tax);
        $this->assertEquals(112100.00, $quotation->total);
    }

    public function test_invoice_and_payment_relationships(): void
    {
        $client = User::create([
            'name' => 'Billing Client',
            'email' => 'billclient@example.com',
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'LMS Platform',
        ]);

        $invoice = Invoice::create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => 100000.00,
            'total' => 118000.00,
            'amount_due' => 118000.00,
        ]);

        $payment = Payment::create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'invoice_id' => $invoice->id,
            'amount' => 50000.00,
            'payment_method' => 'bank_transfer',
        ]);

        $this->assertEquals($client->id, $invoice->client->id);
        $this->assertCount(1, $invoice->payments);
        $this->assertEquals($invoice->id, $payment->invoice->id);
        $this->assertEquals($project->id, $payment->project->id);
    }

    public function test_documents_can_belong_to_client_and_project(): void
    {
        $client = User::create([
            'name' => 'Doc Client',
            'email' => 'docclient@example.com',
            'password' => 'password',
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $project = Project::create([
            'client_id' => $client->id,
            'title' => 'Mobile App',
        ]);

        $document = Document::create([
            'client_id' => $client->id,
            'project_id' => $project->id,
            'uploaded_by_id' => $client->id,
            'original_filename' => 'specifications.pdf',
            'storage_path' => 'documents/specifications.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024500,
        ]);

        $this->assertEquals($client->id, $document->client->id);
        $this->assertEquals($project->id, $document->project->id);
        $this->assertEquals($client->id, $document->uploadedBy->id);
    }
}

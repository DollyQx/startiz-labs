<?php

namespace Tests\Feature\Admin;

use App\Enums\QuotationStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuotationManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $client;
    private User $anotherClient;
    private Project $project;
    private Lead $lead;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->client = User::factory()->create([
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->anotherClient = User::factory()->create([
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->project = Project::factory()->create([
            'client_id' => $this->client->id,
            'title' => 'Web App Development',
        ]);

        $this->lead = Lead::factory()->create([
            'client_id' => $this->client->id,
            'name' => 'John Client Inquiry',
        ]);
    }

    public function test_guests_cannot_access_quotation_management(): void
    {
        $response = $this->get(route('admin.quotations.index'));
        $response->assertRedirect(route('login'));

        $createResponse = $this->get(route('admin.quotations.create'));
        $createResponse->assertRedirect(route('login'));
    }

    public function test_clients_cannot_access_quotation_management(): void
    {
        $response = $this->actingAs($this->client)->get(route('admin.quotations.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_quotation_directory(): void
    {
        Quotation::factory()->count(3)->create([
            'client_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.quotations.index'));

        $response->assertOk();
        $response->assertViewIs('admin.quotations.index');
        $response->assertViewHas('quotations');
    }

    public function test_admin_can_create_quotation_with_financial_recalculation(): void
    {
        $service = Service::factory()->create(['name' => 'Backend Development']);

        $payload = [
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
            'lead_id' => $this->lead->id,
            'issue_date' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(30)->format('Y-m-d'),
            'status' => QuotationStatus::DRAFT->value,
            'notes' => 'Custom proposal notes',
            'terms' => '50% advance, 50% on completion',
            'items' => [
                [
                    'service_id' => $service->id,
                    'description' => 'API Development Scope',
                    'quantity' => 2,
                    'unit_price' => 5000.00, // Line subtotal: 10000
                    'discount' => 1000.00,    // Line discount: 1000
                    'tax' => 1800.00,         // Line tax: 1800 -> Line total: 10800
                    'sequence_order' => 1,
                ],
                [
                    'service_id' => null,
                    'description' => 'Custom UI Integration',
                    'quantity' => 1,
                    'unit_price' => 3000.00, // Line subtotal: 3000
                    'discount' => 0.00,
                    'tax' => 540.00,          // Line tax: 540 -> Line total: 3540
                    'sequence_order' => 2,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.quotations.store'), $payload);

        $quotation = Quotation::latest('id')->first();
        $this->assertNotNull($quotation);

        $response->assertRedirect(route('admin.quotations.show', $quotation->id));
        $response->assertSessionHas('success');

        // Subtotal = 10000 + 3000 = 13000
        // Discount = 1000
        // Tax = 1800 + 540 = 2340
        // Grand Total = 13000 - 1000 + 2340 = 14340
        $this->assertEquals(13000.00, (float) $quotation->subtotal);
        $this->assertEquals(1000.00, (float) $quotation->discount);
        $this->assertEquals(2340.00, (float) $quotation->tax);
        $this->assertEquals(14340.00, (float) $quotation->total);

        $this->assertDatabaseHas('quotation_items', [
            'quotation_id' => $quotation->id,
            'description' => 'API Development Scope',
            'line_total' => 10800.00,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Quotation::class,
            'subject_id' => $quotation->id,
            'action' => 'quotation.created',
        ]);
    }

    public function test_cannot_create_quotation_with_mismatched_project_client(): void
    {
        $payload = [
            'client_id' => $this->anotherClient->id, // Mismatched client!
            'project_id' => $this->project->id, // Belongs to $this->client
            'issue_date' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(15)->format('Y-m-d'),
            'status' => QuotationStatus::DRAFT->value,
            'items' => [
                [
                    'description' => 'Test Item',
                    'quantity' => 1,
                    'unit_price' => 100.00,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.quotations.store'), $payload);

        $response->assertSessionHasErrors(['project_id']);
    }

    public function test_cannot_create_quotation_with_mismatched_lead_client(): void
    {
        $payload = [
            'client_id' => $this->anotherClient->id, // Mismatched client!
            'lead_id' => $this->lead->id, // Belongs to $this->client
            'issue_date' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(15)->format('Y-m-d'),
            'status' => QuotationStatus::DRAFT->value,
            'items' => [
                [
                    'description' => 'Test Item',
                    'quantity' => 1,
                    'unit_price' => 100.00,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.quotations.store'), $payload);

        $response->assertSessionHasErrors(['lead_id']);
    }

    public function test_admin_can_view_quotation_detail_and_activity_logs(): void
    {
        $quotation = Quotation::factory()->create([
            'client_id' => $this->client->id,
            'project_id' => $this->project->id,
        ]);

        QuotationItem::factory()->create([
            'quotation_id' => $quotation->id,
            'description' => 'Sample Item',
            'quantity' => 2,
            'unit_price' => 500.00,
            'line_total' => 1000.00,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.quotations.show', $quotation->id));

        $response->assertOk();
        $response->assertViewIs('admin.quotations.show');
        $response->assertSee($quotation->reference_number);
        $response->assertSee('Sample Item');
    }

    public function test_admin_can_update_editable_quotation(): void
    {
        $quotation = Quotation::factory()->create([
            'client_id' => $this->client->id,
            'status' => QuotationStatus::DRAFT,
        ]);

        $updatePayload = [
            'client_id' => $this->client->id,
            'issue_date' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(20)->format('Y-m-d'),
            'status' => QuotationStatus::SENT->value,
            'notes' => 'Updated notes',
            'items' => [
                [
                    'description' => 'Revised Item Scope',
                    'quantity' => 3,
                    'unit_price' => 2000.00, // 6000
                    'discount' => 500.00,
                    'tax' => 1000.00,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.quotations.update', $quotation->id), $updatePayload);

        $response->assertRedirect(route('admin.quotations.show', $quotation->id));
        
        $quotation->refresh();
        $this->assertEquals(QuotationStatus::SENT, $quotation->status);
        $this->assertEquals(6500.00, (float) $quotation->total); // 6000 - 500 + 1000 = 6500
    }

    public function test_admin_cannot_edit_finalized_accepted_quotation_directly(): void
    {
        $quotation = Quotation::factory()->create([
            'client_id' => $this->client->id,
            'status' => QuotationStatus::ACCEPTED,
        ]);

        $editResponse = $this->actingAs($this->admin)->get(route('admin.quotations.edit', $quotation->id));
        $editResponse->assertRedirect(route('admin.quotations.show', $quotation->id));
        $editResponse->assertSessionHas('error');

        $updatePayload = [
            'client_id' => $this->client->id,
            'issue_date' => now()->format('Y-m-d'),
            'valid_until' => now()->addDays(20)->format('Y-m-d'),
            'status' => QuotationStatus::ACCEPTED->value,
            'items' => [
                [
                    'description' => 'Illegal Edit',
                    'quantity' => 1,
                    'unit_price' => 1000.00,
                ]
            ]
        ];

        $updateResponse = $this->actingAs($this->admin)->put(route('admin.quotations.update', $quotation->id), $updatePayload);
        $updateResponse->assertSessionHasErrors(['status']);
    }

    public function test_admin_can_update_quotation_status(): void
    {
        $quotation = Quotation::factory()->create([
            'client_id' => $this->client->id,
            'status' => QuotationStatus::DRAFT,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.quotations.status', $quotation->id), [
            'status' => QuotationStatus::ACCEPTED->value,
        ]);

        $response->assertRedirect(route('admin.quotations.show', $quotation->id));

        $quotation->refresh();
        $this->assertEquals(QuotationStatus::ACCEPTED, $quotation->status);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Quotation::class,
            'subject_id' => $quotation->id,
            'action' => 'quotation.status_updated',
        ]);
    }
}

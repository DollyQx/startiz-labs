<?php

namespace Tests\Feature\Admin;

use App\Enums\LeadStatus;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ActivityLog;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);
    }

    public function test_admin_can_view_leads_pipeline_list(): void
    {
        $lead = Lead::factory()->create([
            'name' => 'Acme Lead Inquiry',
            'status' => LeadStatus::NEW->value,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.leads.index'))
            ->assertStatus(200)
            ->assertSee('Acme Lead Inquiry')
            ->assertSee($lead->reference_number);
    }

    public function test_admin_can_render_lead_creation_form(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.leads.create'))
            ->assertStatus(200)
            ->assertSee('Create Lead Proposal');
    }

    public function test_admin_can_create_new_lead_proposal(): void
    {
        $payload = [
            'name' => 'John Wick',
            'email' => 'john@continental.test',
            'phone' => '+91 9999988888',
            'company_name' => 'High Table Inc',
            'source' => 'referral',
            'status' => 'new',
            'notes' => 'Custom software development for security tracking.',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('admin.leads.store'), $payload);

        $this->assertDatabaseHas('leads', [
            'name' => 'John Wick',
            'email' => 'john@continental.test',
            'company_name' => 'High Table Inc',
            'status' => 'new',
        ]);

        $lead = Lead::where('email', 'john@continental.test')->first();
        $this->assertNotNull($lead->reference_number);
        $this->assertStringStartsWith('STZ-LEAD', $lead->reference_number);

        $response->assertRedirect(route('admin.leads.show', $lead->id))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'action' => 'lead.created',
        ]);
    }

    public function test_lead_creation_fails_with_invalid_validation_data(): void
    {
        $payload = [
            'name' => '',
            'email' => 'invalid-email',
            'status' => 'invalid_status_value',
        ];

        $this->actingAs($this->admin)
            ->post(route('admin.leads.store'), $payload)
            ->assertSessionHasErrors(['name', 'email', 'status']);
    }

    public function test_admin_can_view_lead_detail_page(): void
    {
        $lead = Lead::factory()->create([
            'name' => 'Robert Paulson',
            'email' => 'robert@fightclub.test',
            'company_name' => 'Paper Street Soap',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.leads.show', $lead->id))
            ->assertStatus(200)
            ->assertSee($lead->reference_number)
            ->assertSee('Robert Paulson')
            ->assertSee('Paper Street Soap');
    }

    public function test_admin_can_update_lead_information(): void
    {
        $lead = Lead::factory()->create([
            'name' => 'Original Name',
            'status' => LeadStatus::NEW->value,
        ]);

        $payload = [
            'name' => 'Updated Name',
            'email' => $lead->email,
            'company_name' => 'Updated Enterprise',
            'source' => 'website',
            'status' => 'contacted',
            'notes' => 'Updated scope notes.',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.leads.update', $lead->id), $payload);

        $response->assertRedirect(route('admin.leads.show', $lead->id));

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'name' => 'Updated Name',
            'company_name' => 'Updated Enterprise',
            'status' => 'contacted',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'action' => 'lead.updated',
        ]);
    }

    public function test_admin_can_transition_lead_pipeline_status(): void
    {
        $lead = Lead::factory()->create([
            'status' => LeadStatus::NEW->value,
        ]);

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.leads.status', $lead->id), [
                'status' => 'qualified',
            ]);

        $response->assertRedirect(route('admin.leads.show', $lead->id));

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => 'qualified',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'subject_type' => Lead::class,
            'subject_id' => $lead->id,
            'action' => 'lead.status_updated',
        ]);
    }
}

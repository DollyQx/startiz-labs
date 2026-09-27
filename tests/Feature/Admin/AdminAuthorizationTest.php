<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_admin_crm_routes(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
        $this->get(route('admin.clients.index'))->assertRedirect(route('login'));
        $this->get(route('admin.leads.index'))->assertRedirect(route('login'));
        $this->get(route('admin.leads.create'))->assertRedirect(route('login'));
        $this->get(route('admin.projects.index'))->assertRedirect(route('login'));
        $this->get(route('admin.quotations.index'))->assertRedirect(route('login'));
    }

    public function test_clients_cannot_access_admin_crm_routes(): void
    {
        $client = User::factory()->create([
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->actingAs($client)
            ->get(route('admin.dashboard'))
            ->assertStatus(403);

        $this->actingAs($client)
            ->get(route('admin.clients.index'))
            ->assertStatus(403);

        $this->actingAs($client)
            ->get(route('admin.leads.index'))
            ->assertStatus(403);

        $this->actingAs($client)
            ->get(route('admin.leads.create'))
            ->assertStatus(403);

        $this->actingAs($client)
            ->get(route('admin.projects.index'))
            ->assertStatus(403);

        $this->actingAs($client)
            ->get(route('admin.quotations.index'))
            ->assertStatus(403);
    }

    public function test_authorized_admins_can_access_admin_crm_routes(): void
    {
        $admin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200)
            ->assertSee('CRM Dashboard Overview');

        $this->actingAs($admin)
            ->get(route('admin.clients.index'))
            ->assertStatus(200)
            ->assertSee('Clients Directory');

        $this->actingAs($admin)
            ->get(route('admin.leads.index'))
            ->assertStatus(200)
            ->assertSee('Leads Pipeline');

        $this->actingAs($admin)
            ->get(route('admin.projects.index'))
            ->assertStatus(200)
            ->assertSee('Project Directory');

        $this->actingAs($admin)
            ->get(route('admin.quotations.index'))
            ->assertStatus(200)
            ->assertSee('Quotations Directory');
    }

    public function test_super_admins_can_access_admin_crm_routes(): void
    {
        $superAdmin = User::factory()->create([
            'role' => UserRole::SUPER_ADMIN->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->actingAs($superAdmin)
            ->get(route('admin.dashboard'))
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get(route('admin.clients.index'))
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get(route('admin.leads.index'))
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get(route('admin.projects.index'))
            ->assertStatus(200);

        $this->actingAs($superAdmin)
            ->get(route('admin.quotations.index'))
            ->assertStatus(200);
    }
}

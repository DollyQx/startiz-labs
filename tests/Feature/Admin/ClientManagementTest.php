<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientManagementTest extends TestCase
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

    public function test_admin_can_view_client_directory_list(): void
    {
        $client = User::factory()->create([
            'name' => 'Acme Corp Client',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.clients.index'))
            ->assertStatus(200)
            ->assertSee('Acme Corp Client');
    }

    public function test_admin_can_search_clients_by_name_or_email(): void
    {
        $clientA = User::factory()->create([
            'name' => 'Alpha Solutions',
            'email' => 'alpha@solutions.test',
            'role' => UserRole::CLIENT->value,
        ]);

        $clientB = User::factory()->create([
            'name' => 'Beta Logistics',
            'email' => 'beta@logistics.test',
            'role' => UserRole::CLIENT->value,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.clients.index', ['search' => 'Alpha']))
            ->assertStatus(200)
            ->assertSee('Alpha Solutions')
            ->assertDontSee('Beta Logistics');
    }

    public function test_admin_can_filter_clients_by_status(): void
    {
        $activeClient = User::factory()->create([
            'name' => 'Active Business User',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        $suspendedClient = User::factory()->create([
            'name' => 'Suspended Business User',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::SUSPENDED->value,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.clients.index', ['status' => 'suspended']))
            ->assertStatus(200)
            ->assertSee('Suspended Business User')
            ->assertDontSee('Active Business User');
    }

    public function test_admin_can_view_individual_client_profile(): void
    {
        $client = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'role' => UserRole::CLIENT->value,
            'status' => UserStatus::ACTIVE->value,
        ]);

        ClientProfile::factory()->create([
            'user_id' => $client->id,
            'company_name' => 'JaneTech Enterprises',
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.clients.show', $client->id))
            ->assertStatus(200)
            ->assertSee('Jane Doe')
            ->assertSee('JaneTech Enterprises')
            ->assertSee('Account Information');
    }

    public function test_non_client_user_id_returns_404_on_client_profile(): void
    {
        $anotherAdmin = User::factory()->create([
            'role' => UserRole::ADMIN->value,
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.clients.show', $anotherAdmin->id))
            ->assertStatus(404);
    }
}

<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\ClientProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CrossClientDataAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_access_their_own_profile(): void
    {
        $clientA = User::create([
            'name' => 'Client A',
            'email' => 'clienta@example.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $profileA = ClientProfile::create([
            'user_id' => $clientA->id,
            'company_name' => 'Company A',
        ]);

        $this->actingAs($clientA);

        $this->assertTrue(Gate::allows('view', $profileA));
        $this->assertTrue(Gate::allows('update', $profileA));

        $response = $this->get('/client/profile');
        $response->assertStatus(200);
        $response->assertSee('Company A');
    }

    public function test_client_a_cannot_access_or_update_client_b_profile(): void
    {
        $clientA = User::create([
            'name' => 'Client A',
            'email' => 'clienta@example.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $clientB = User::create([
            'name' => 'Client B',
            'email' => 'clientb@example.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $profileB = ClientProfile::create([
            'user_id' => $clientB->id,
            'company_name' => 'Company B',
        ]);

        $this->actingAs($clientA);

        $this->assertFalse(Gate::allows('view', $profileB));
        $this->assertFalse(Gate::allows('update', $profileB));
    }

    public function test_admin_can_view_any_client_profile(): void
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@startizlabs.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        $client = User::create([
            'name' => 'Client Target',
            'email' => 'target@example.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $profile = ClientProfile::create([
            'user_id' => $client->id,
            'company_name' => 'Target Company',
        ]);

        $this->actingAs($admin);

        $this->assertTrue(Gate::allows('view', $profile));
        $this->assertTrue(Gate::allows('update', $profile));
    }
}

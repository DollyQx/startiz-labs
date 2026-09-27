<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UnauthorizedAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guests_cannot_access_client_dashboard(): void
    {
        $response = $this->get('/client/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_clients_cannot_access_admin_dashboard(): void
    {
        $client = User::create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($client);

        $response = $this->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    public function test_suspended_users_are_denied_access(): void
    {
        $suspendedUser = User::create([
            'name' => 'Suspended User',
            'email' => 'suspended@example.com',
            'password' => Hash::make('password123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::SUSPENDED,
        ]);

        $this->actingAs($suspendedUser);

        $response = $this->get('/client/dashboard');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }
}

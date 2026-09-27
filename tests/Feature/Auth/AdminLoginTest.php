<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Admin Portal Sign In');
    }

    public function test_admin_users_can_authenticate_via_admin_login_screen(): void
    {
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@startizlabs.com',
            'password' => Hash::make('AdminPass123'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'admin@startizlabs.com',
            'password' => 'AdminPass123',
        ]);

        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect('/admin/dashboard');
    }

    public function test_client_users_cannot_authenticate_via_admin_login(): void
    {
        User::create([
            'name' => 'Regular Client',
            'email' => 'client@example.com',
            'password' => Hash::make('ClientPass123'),
            'role' => UserRole::CLIENT,
            'status' => UserStatus::ACTIVE,
        ]);

        $response = $this->post('/admin/login', [
            'email' => 'client@example.com',
            'password' => 'ClientPass123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_admin_can_logout(): void
    {
        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@startizlabs.com',
            'password' => Hash::make('AdminPass123'),
            'role' => UserRole::ADMIN,
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($admin);

        $response = $this->post('/admin/logout');

        $this->assertGuest();
        $response->assertRedirect('/admin/login');
    }
}

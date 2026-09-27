<?php

namespace Tests\Feature\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Create Client Account');
    }

    public function test_new_clients_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'phone' => '+91 9876543210',
            'company_name' => 'Test Agency',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/client/dashboard');

        $user = User::where('email', 'client@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(UserRole::CLIENT, $user->role);
        $this->assertEquals(UserStatus::ACTIVE, $user->status);
        $this->assertNotNull($user->clientProfile);
        $this->assertEquals('Test Agency', $user->clientProfile->company_name);
    }

    public function test_registration_fails_with_existing_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->post('/register', [
            'name' => 'Another Client',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }
}

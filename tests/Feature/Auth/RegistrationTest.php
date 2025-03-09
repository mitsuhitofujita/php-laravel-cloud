<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->assertDatabaseCount('observers', 0);
        $this->assertDatabaseCount('observer_details', 0);

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseCount('observers', 1);
        $this->assertDatabaseCount('observer_details', 1);
        $this->assertDatabaseHas('observer_details', [
            'name' => 'Test User',
        ]);
        $this->assertDatabaseCount('organizations', 1);
        $this->assertDatabaseCount('organization_details', 1);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}

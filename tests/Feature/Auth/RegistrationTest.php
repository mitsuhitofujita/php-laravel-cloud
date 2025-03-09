<?php

namespace Tests\Feature\Auth;

use App\Events\UserCreated;
use App\Events\ObserverCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
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
        Event::fakeFor(function () {
            $response = $this->post('/register', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            Event::assertDispatched(UserCreated::class);

            $this->assertAuthenticated();
            $response->assertRedirect(route('dashboard', absolute: false));
        }, [UserCreated::class, ObserverCreated::class]);
    }
}

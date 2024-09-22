<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/users/create');
        $response->assertRedirectToRoute('login');

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/users/create');

        $response->assertOk();

        $response->assertStatus(200);
    }

    public function test_new_users_can_register()
    {
        $params = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/users', $params);
        $response->assertRedirectToRoute('login');

        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/users/create');

        $response = $this->post('/users', $params);

        $this->assertModelExists($user);
        $response->assertRedirectToRoute('users.index');
    }
}

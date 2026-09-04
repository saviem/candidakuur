<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_is_public(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Weet je straks precies wat je mag eten')
            ->assertSee('Account aanmaken')
            ->assertSee('Zet dit op je beginscherm')
            ->assertSee('Zet op beginscherm')
            ->assertSee('manifest.json', false)
            ->assertSee('apple-touch-icon', false);
    }

    public function test_guests_cannot_open_the_knowledge_base(): void
    {
        $this->get('/kennisbank')->assertRedirect(route('login'));
        $this->get('/zoeken')->assertRedirect(route('login'));
        $this->get('/categorieen')->assertRedirect(route('login'));
        $this->get('/menu')->assertRedirect(route('login'));
        $this->get('/gids')->assertRedirect(route('login'));
    }

    public function test_users_can_register_and_reach_the_knowledge_base(): void
    {
        $this->get('/account')->assertOk();

        $this->post('/account', [
            '_token' => session()->token(),
            'name' => 'Anna',
            'email' => 'anna@example.com',
            'password' => 'wachtwoord',
            'password_confirmation' => 'wachtwoord',
        ])->assertRedirect(route('kennisbank'));

        $this->assertAuthenticated();
        $this->get('/kennisbank')->assertOk();
        $this->assertFalse(User::query()->where('email', 'anna@example.com')->value('is_admin'));
    }

    public function test_users_can_log_in(): void
    {
        $user = User::factory()->create([
            'password' => 'wachtwoord',
        ]);

        $this->get('/inloggen')->assertOk();

        $this->post('/inloggen', [
            '_token' => session()->token(),
            'email' => $user->email,
            'password' => 'wachtwoord',
        ])->assertRedirect(route('kennisbank'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_regular_users_cannot_open_admin(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();
    }
}

<?php

namespace Tests\Feature;

use App\Mail\LoginCodeMail;
use App\Models\LoginCode;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_is_public(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Voel je weer helder')
            ->assertSee('Begin met de kuur')
            ->assertSee('Last van dit? Doe dan drie weken de kuur')
            ->assertSee('Saviem Jansen')
            ->assertSee('Productzoeken')
            ->assertSee('Koelkast-assistent')
            ->assertSee('Wat nu?')
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
        $this->get('/assistent')->assertRedirect(route('login'));
        $this->get('/gids')->assertRedirect(route('login'));
        $this->get('/wat-nu')->assertRedirect(route('login'));
        $this->get('/dagboek')->assertRedirect(route('login'));
    }

    public function test_users_can_register_with_email_code(): void
    {
        Mail::fake();

        $this->get('/account')->assertOk();

        $this->post('/account', [
            '_token' => session()->token(),
            'name' => 'Anna',
            'email' => 'anna@example.com',
        ])->assertRedirect(route('login.verify'));

        Mail::assertSent(LoginCodeMail::class, function (LoginCodeMail $mail): bool {
            return $mail->hasTo('anna@example.com');
        });

        $code = '123456';
        LoginCode::query()->where('email', 'anna@example.com')->update([
            'code' => Hash::make($code),
        ]);

        $this->post('/inloggen/code', [
            '_token' => session()->token(),
            'code' => $code,
        ])->assertRedirect(route('kennisbank'));

        $this->assertAuthenticated();
        $this->get('/kennisbank')->assertOk();
        $this->assertDatabaseHas('users', [
            'email' => 'anna@example.com',
            'name' => 'Anna',
            'is_admin' => false,
        ]);
        $this->assertNull(User::query()->where('email', 'anna@example.com')->value('password'));
    }

    public function test_users_can_log_in_with_email_code(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'lisa@example.com',
        ]);

        $this->get('/inloggen')->assertOk();

        $this->post('/inloggen', [
            '_token' => session()->token(),
            'email' => $user->email,
        ])->assertRedirect(route('login.verify'));

        Mail::assertSent(LoginCodeMail::class);

        $code = '654321';
        LoginCode::query()->where('email', $user->email)->update([
            'code' => Hash::make($code),
        ]);

        $this->post('/inloggen/code', [
            '_token' => session()->token(),
            'code' => $code,
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

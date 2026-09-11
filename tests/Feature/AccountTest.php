<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_and_update_account(): void
    {
        $user = User::factory()->create([
            'name' => 'Oud',
            'email' => 'oud@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Mijn account')
            ->assertSee('Account verwijderen');

        $this->actingAs($user)
            ->put(route('account.update'), [
                'name' => 'Nieuw',
                'email' => 'nieuw@example.com',
            ])
            ->assertRedirect(route('account.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nieuw',
            'email' => 'nieuw@example.com',
        ]);
    }

    public function test_user_can_delete_account_with_email_confirmation(): void
    {
        $user = User::factory()->create([
            'email' => 'weg@example.com',
        ]);

        $this->actingAs($user)
            ->delete(route('account.destroy'), [
                'confirm_email' => 'weg@example.com',
            ])
            ->assertRedirect(route('home'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'weg@example.com']);
    }

    public function test_menu_lives_under_gids(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('menu'))
            ->assertOk()
            ->assertSee('Menu voor 3 weken');

        $this->actingAs($user)
            ->get('/menu')
            ->assertRedirect('/gids/menu');

        $this->actingAs($user)
            ->get(route('guides.index'))
            ->assertOk()
            ->assertSee('Voorbeeldmenu (3 weken)')
            ->assertSee(route('menu'), false);
    }

    public function test_plus_section_on_account_when_subscribed(): void
    {
        $user = User::factory()->create([
            'plus_until' => now()->addMonth(),
        ]);

        $this->actingAs($user)
            ->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Je hebt Plus')
            ->assertSee('Activeer code')
            ->assertDontSee('Bekijk Plus');
    }

    public function test_non_plus_user_sees_upgrade_cta_on_account(): void
    {
        $user = User::factory()->create(['plus_until' => null]);

        $this->actingAs($user)
            ->get(route('account.edit'))
            ->assertOk()
            ->assertSee('Nog geen Plus')
            ->assertSee('Bekijk Plus');
    }
}

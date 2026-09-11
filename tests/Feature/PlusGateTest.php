<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlusGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_free_users_can_view_diary_but_cannot_store(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('diary.index'))
            ->assertOk()
            ->assertSee('Dagboek')
            ->assertSee('Invullen hoort bij Candidakuur Plus');

        $this->actingAs($user)
            ->post(route('diary.store'), [
                'entry_date' => now()->toDateString(),
                'mood' => 3,
                'energy' => 3,
            ])
            ->assertRedirect(route('plus.index'));
    }

    public function test_plus_users_can_open_diary(): void
    {
        $user = User::factory()->plus()->create();

        $this->actingAs($user)
            ->get(route('diary.index'))
            ->assertOk()
            ->assertSee('Dagboek')
            ->assertDontSee('Invullen hoort bij Candidakuur Plus');
    }

    public function test_plus_page_shows_config_message_without_mollie_key(): void
    {
        config(['services.mollie.key' => null]);

        $this->actingAs(User::factory()->create())
            ->get(route('plus.index'))
            ->assertOk()
            ->assertSee('MOLLIE_KEY');
    }

    public function test_gate_plus_allows_only_active_subscribers(): void
    {
        $free = User::factory()->create();
        $plus = User::factory()->plus()->create();

        $this->assertFalse($free->can('plus'));
        $this->assertTrue($plus->can('plus'));
    }
}

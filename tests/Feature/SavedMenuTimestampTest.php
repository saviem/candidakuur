<?php

namespace Tests\Feature;

use App\Models\SavedMenu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SavedMenuTimestampTest extends TestCase
{
    use RefreshDatabase;

    public function test_saved_menu_list_shows_europe_amsterdam_time(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-09-09 19:51:00', 'Europe/Amsterdam'));

        $user = User::factory()->create();

        SavedMenu::query()->create([
            'user_id' => $user->id,
            'title' => 'Prei en Aardappelen Dagmenu',
            'products' => [['name' => 'Prei', 'slug' => 'prei']],
            'payload' => [
                'title' => 'Prei en Aardappelen Dagmenu',
                'intro' => 'Bewaard menu',
                'meals' => [],
                'tips' => [],
                'warning' => null,
                'products' => [],
            ],
        ]);

        $this->actingAs($user)
            ->get(route('assistant'))
            ->assertOk()
            ->assertSee('Prei en Aardappelen Dagmenu')
            ->assertSee('09-09 19:51');
    }
}

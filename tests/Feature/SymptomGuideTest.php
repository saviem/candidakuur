<?php

namespace Tests\Feature;

use App\Models\SymptomGuide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SymptomGuideTest extends TestCase
{
    use RefreshDatabase;

    private function seedGuide(): SymptomGuide
    {
        return SymptomGuide::query()->create([
            'slug' => 'hoofdpijn',
            'title' => 'Hoofdpijn',
            'tags' => ['hoofdpijn'],
            'summary' => 'Korte teaser over hoofdpijn.',
            'body_common' => 'Komt vaker voor tekst.',
            'body_practical' => 'Praktische hulp tekst.',
            'body_contact' => 'Contact tekst.',
            'published' => true,
            'sort_order' => 1,
        ]);
    }

    public function test_auth_users_see_wat_nu_index(): void
    {
        $this->seedGuide();

        $this->actingAs(User::factory()->create())
            ->get(route('symptoms.index'))
            ->assertOk()
            ->assertSee('Wat nu?')
            ->assertSee('Hoofdpijn')
            ->assertSee('Medische disclaimer');
    }

    public function test_free_users_see_teaser_only_on_detail(): void
    {
        $this->seedGuide();

        $this->actingAs(User::factory()->create())
            ->get(route('symptoms.show', 'hoofdpijn'))
            ->assertOk()
            ->assertSee('Korte teaser over hoofdpijn.')
            ->assertSee('Teaser')
            ->assertDontSee('Komt vaker voor tekst.')
            ->assertSee('Bekijk Plus');
    }

    public function test_plus_users_see_full_symptom_guide(): void
    {
        $this->seedGuide();

        $this->actingAs(User::factory()->plus()->create())
            ->get(route('symptoms.show', 'hoofdpijn'))
            ->assertOk()
            ->assertSee('Komt vaker voor tekst.')
            ->assertSee('Praktische hulp tekst.')
            ->assertSee('Contact tekst.');
    }

    public function test_guests_cannot_open_wat_nu(): void
    {
        $this->get(route('symptoms.index'))->assertRedirect(route('login'));
    }
}

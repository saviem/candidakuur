<?php

namespace Tests\Feature;

use App\Models\DiaryEntry;
use App\Models\SymptomGuide;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DiaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_plus_user_can_store_a_daily_entry(): void
    {
        $user = User::factory()->plus()->create();

        $this->actingAs($user)
            ->post(route('diary.store'), [
                'entry_date' => now()->toDateString(),
                'mood' => 4,
                'energy' => 3,
                'symptom_tags' => ['hoofdpijn', 'moeheid'],
                'note' => 'Redelijk oké',
                'meals' => 'Omelet',
            ])
            ->assertRedirect(route('diary.index'));

        $this->assertDatabaseHas('diary_entries', [
            'user_id' => $user->id,
            'mood' => 4,
            'energy' => 3,
        ]);
    }

    public function test_user_cannot_update_someone_elses_entry(): void
    {
        $owner = User::factory()->plus()->create();
        $other = User::factory()->plus()->create();

        $entry = DiaryEntry::query()->create([
            'user_id' => $owner->id,
            'entry_date' => now()->toDateString(),
            'mood' => 2,
            'energy' => 2,
            'symptom_tags' => ['jeuk'],
        ]);

        $this->actingAs($other)
            ->put(route('diary.update', $entry), [
                'entry_date' => now()->toDateString(),
                'mood' => 5,
                'energy' => 5,
            ])
            ->assertForbidden();
    }

    public function test_repeating_tags_suggest_wat_nu_cards(): void
    {
        $user = User::factory()->plus()->create();

        SymptomGuide::query()->create([
            'slug' => 'hoofdpijn',
            'title' => 'Hoofdpijn',
            'tags' => ['hoofdpijn'],
            'summary' => 'Samenvatting',
            'body_common' => 'Common',
            'body_practical' => 'Practical',
            'body_contact' => 'Contact',
            'published' => true,
            'sort_order' => 1,
        ]);

        DiaryEntry::query()->create([
            'user_id' => $user->id,
            'entry_date' => now()->subDays(2)->toDateString(),
            'mood' => 2,
            'energy' => 2,
            'symptom_tags' => ['hoofdpijn'],
        ]);
        DiaryEntry::query()->create([
            'user_id' => $user->id,
            'entry_date' => now()->subDay()->toDateString(),
            'mood' => 2,
            'energy' => 2,
            'symptom_tags' => ['hoofdpijn'],
        ]);

        $this->actingAs($user)
            ->get(route('diary.index'))
            ->assertOk()
            ->assertSee('Hoofdpijn')
            ->assertSee('Suggesties');
    }

    public function test_entry_date_is_unique_per_user(): void
    {
        $user = User::factory()->plus()->create();
        $date = now()->toDateString();

        $this->actingAs($user)->post(route('diary.store'), [
            'entry_date' => $date,
            'mood' => 3,
            'energy' => 3,
        ])->assertRedirect();

        $this->actingAs($user)->post(route('diary.store'), [
            'entry_date' => $date,
            'mood' => 5,
            'energy' => 4,
            'note' => 'Update',
        ])->assertRedirect();

        $this->assertSame(1, DiaryEntry::query()->where('user_id', $user->id)->count());
        $this->assertSame(5, DiaryEntry::query()->where('user_id', $user->id)->value('mood'));
    }
}

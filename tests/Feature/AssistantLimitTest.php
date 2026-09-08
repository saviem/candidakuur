<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\SavedMenu;
use App\Models\User;
use App\Services\AssistantUsageLimiter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AssistantLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedProducts();
    }

    public function test_free_user_is_limited_to_three_generations_per_day(): void
    {
        $user = User::factory()->create();
        $limiter = app(AssistantUsageLimiter::class);

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'title' => 'Testmenu',
                            'intro' => 'Intro',
                            'meals' => [
                                ['label' => 'Ontbijt', 'text' => 'Ei met courgette.', 'note' => null],
                            ],
                            'tips' => [],
                            'warning' => null,
                        ], JSON_THROW_ON_ERROR),
                    ],
                ]],
            ]),
        ]);

        for ($i = 0; $i < AssistantUsageLimiter::FREE_DAILY_LIMIT; $i++) {
            $this->actingAs($user)
                ->post(route('assistant.store'), [
                    'one' => 'Courgette',
                    'two' => 'Ei',
                ])
                ->assertOk();
        }

        $this->assertSame(AssistantUsageLimiter::FREE_DAILY_LIMIT, $limiter->usedToday($user));
        $this->assertSame(AssistantUsageLimiter::FREE_DAILY_LIMIT, SavedMenu::query()->where('user_id', $user->id)->count());

        $this->actingAs($user)
            ->from(route('assistant'))
            ->post(route('assistant.store'), [
                'one' => 'Courgette',
                'two' => 'Ei',
            ])
            ->assertRedirect(route('assistant'))
            ->assertSessionHasErrors('one');
    }

    public function test_plus_user_has_unlimited_generations(): void
    {
        $user = User::factory()->plus()->create();

        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'title' => 'Plusmenu',
                            'intro' => 'Intro',
                            'meals' => [
                                ['label' => 'Lunch', 'text' => 'Soep.', 'note' => null],
                            ],
                            'tips' => [],
                            'warning' => null,
                        ], JSON_THROW_ON_ERROR),
                    ],
                ]],
            ]),
        ]);

        for ($i = 0; $i < 4; $i++) {
            $this->actingAs($user)
                ->post(route('assistant.store'), [
                    'one' => 'Courgette',
                    'two' => 'Ei',
                ])
                ->assertOk()
                ->assertSee('Plusmenu');
        }

        $this->assertSame(4, SavedMenu::query()->where('user_id', $user->id)->count());
    }

    public function test_saved_menu_can_be_reopened(): void
    {
        $user = User::factory()->create();
        $saved = SavedMenu::query()->create([
            'user_id' => $user->id,
            'title' => 'Courgette-omelet',
            'products' => [['name' => 'Courgette', 'slug' => 'courgette']],
            'payload' => [
                'title' => 'Courgette-omelet',
                'intro' => 'Bewaard menu',
                'meals' => [['label' => 'Ontbijt', 'text' => 'Omelet', 'note' => null]],
                'tips' => [],
                'warning' => null,
                'products' => [['name' => 'Courgette', 'slug' => 'courgette', 'product' => null]],
            ],
        ]);

        $this->actingAs($user)
            ->get(route('assistant', ['saved' => $saved->id]))
            ->assertOk()
            ->assertSee('Courgette-omelet')
            ->assertSee('Bewaard menu');
    }

    private function seedProducts(): void
    {
        $category = Category::query()->create([
            'slug' => 'groente',
            'name' => 'Groente',
            'intro' => 'Groente tijdens de kuur.',
            'sort_order' => 1,
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'courgette',
            'name' => 'Courgette',
            'status' => ProductStatus::Toegestaan,
            'why' => 'Licht verteerbaar.',
            'aliases' => ['zucchini'],
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'ei',
            'name' => 'Ei',
            'status' => ProductStatus::Toegestaan,
            'why' => 'Eiwitbron.',
            'aliases' => ['eieren'],
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AssistantTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());
        $this->seedProducts();
    }

    public function test_assistant_page_is_shown(): void
    {
        $this->get(route('assistant'))
            ->assertOk()
            ->assertSee('Wat ligt er in je koelkast?')
            ->assertSee('Eerste product')
            ->assertSee('Tweede product');
    }

    public function test_two_different_products_are_required(): void
    {
        $this->from(route('assistant'))
            ->post(route('assistant.store'), [
                'one' => 'Courgette',
                'two' => 'Courgette',
            ])
            ->assertRedirect(route('assistant'))
            ->assertSessionHasErrors('two');
    }

    public function test_it_builds_a_menu_from_two_fridge_products(): void
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response([
                'choices' => [[
                    'message' => [
                        'content' => json_encode([
                            'title' => 'Courgette-omelet',
                            'intro' => 'Twee toegestane producten in één dagmenu.',
                            'meals' => [
                                ['label' => 'Ontbijt', 'text' => 'Omelet van ei met gebakken courgette.', 'note' => null],
                                ['label' => 'Lunch', 'text' => 'Courgettesoep met een gekookt ei.', 'note' => 'Klein portie.'],
                            ],
                            'tips' => ['Kook vers.'],
                            'warning' => null,
                        ], JSON_THROW_ON_ERROR),
                    ],
                ]],
            ]),
        ]);

        $this->post(route('assistant.store'), [
            'one' => 'Courgette',
            'two' => 'Ei',
            'one_slug' => 'courgette',
            'two_slug' => 'ei',
        ])
            ->assertOk()
            ->assertSee('Courgette-omelet')
            ->assertSee('Omelet van ei met gebakken courgette.')
            ->assertSee('Courgette')
            ->assertSee('Ei');

        Http::assertSent(fn ($request) => str_contains($request->body(), 'Courgette')
            && str_contains($request->body(), 'Ei')
            && str_contains($request->body(), 'toegestaan'));
    }

    public function test_it_shows_an_error_when_the_assistant_is_unavailable(): void
    {
        Http::fake([
            'https://openrouter.ai/api/v1/chat/completions' => Http::response(['error' => 'unavailable'], 500),
        ]);

        $this->from(route('assistant'))
            ->post(route('assistant.store'), [
                'one' => 'Courgette',
                'two' => 'Ei',
            ])
            ->assertRedirect(route('assistant'))
            ->assertSessionHasErrors('one');
    }

    public function test_product_suggestions_can_be_limited_to_products(): void
    {
        $this->getJson(route('search.suggestions', ['q' => 'cour', 'kind' => 'product']))
            ->assertOk()
            ->assertJsonFragment([
                'kind' => 'product',
                'title' => 'Courgette',
                'slug' => 'courgette',
            ]);
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
            'why' => 'Licht verteerbaar en toegestaan tijdens de kuur.',
            'aliases' => ['zucchini'],
        ]);

        Product::query()->create([
            'category_id' => $category->id,
            'slug' => 'ei',
            'name' => 'Ei',
            'status' => ProductStatus::Toegestaan,
            'why' => 'Eiwitbron die in beperkte hoeveelheid past.',
            'aliases' => ['eieren'],
        ]);
    }
}

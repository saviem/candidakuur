<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AllergyPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::factory()->create());
    }

    public function test_allergies_index_lists_main_allergens(): void
    {
        $this->get(route('allergies.index'))
            ->assertOk()
            ->assertSee('Gluten')
            ->assertSee('Zuivel')
            ->assertSee('Snelle suikers')
            ->assertSee('Ei')
            ->assertSee('Histamine');
    }

    public function test_gluten_page_shows_related_products(): void
    {
        $this->get('/allergieen/gluten')
            ->assertOk()
            ->assertSee('Speltzuurdesembrood')
            ->assertSee('Teffmeel')
            ->assertSee('Volkorenpasta');
    }

    public function test_product_page_links_to_allergies(): void
    {
        $this->get('/product/koemelk')
            ->assertOk()
            ->assertSee('Zuivel')
            ->assertSee('/allergieen/zuivel');
    }

    public function test_product_pages_resolve_by_slug(): void
    {
        $this->get('/product/zilvervliesrijst')
            ->assertOk()
            ->assertSee('Zilvervliesrijst')
            ->assertSee('Te zwaar af te breken tijdens de kuur.');
    }

    public function test_search_suggests_allergies(): void
    {
        $this->getJson(route('search.suggestions', ['q' => 'glut']))
            ->assertOk()
            ->assertJsonFragment([
                'kind' => 'allergy',
                'title' => 'Gluten',
            ]);
    }
}

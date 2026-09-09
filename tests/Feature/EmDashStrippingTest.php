<?php

namespace Tests\Feature;

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmDashStrippingTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_why_strips_em_dashes_on_save(): void
    {
        $category = Category::query()->create([
            'slug' => 'test-cat-emdash',
            'name' => 'Testcategorie',
            'intro' => 'Intro zonder streepjes',
        ]);

        $em = "\u{2014}";

        $product = Product::query()->create([
            'category_id' => $category->id,
            'name' => 'Testproduct EmDash',
            'slug' => 'testproduct-emdash',
            'status' => ProductStatus::Toegestaan,
            'why' => "Eerst dit {$em} dan dat.",
            'access' => 'public',
        ]);

        $this->assertSame('Eerst dit, dan dat.', $product->fresh()->why);
        $this->assertStringNotContainsString($em, (string) $product->fresh()->why);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Allergy;
use App\Models\Category;
use App\Models\Guide;
use App\Models\GuideSection;
use App\Models\MenuDay;
use App\Models\MenuMeal;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'saviem@pureorange.nl')],
            [
                'name' => 'ARDRA',
                'password' => env('ADMIN_PASSWORD', 'voedingsadvies'),
                'is_admin' => true,
            ],
        );

        $this->seedCategoriesAndProducts();
        $this->seedAllergies();
        $this->seedGuides();
        $this->seedMenu();
    }

    private function seedCategoriesAndProducts(): void
    {
        $categories = $this->json('categories.json');
        $categoryIds = [];

        foreach ($categories as $index => $row) {
            $category = Category::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'short' => $row['short'] ?? null,
                    'intro' => $row['intro'],
                    'access' => $row['access'] ?? 'public',
                    'sort_order' => $index,
                ],
            );
            $categoryIds[$row['slug']] = $category->id;
        }

        foreach ($this->json('products.json') as $row) {
            Product::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'category_id' => $categoryIds[$row['category']] ?? null,
                    'name' => $row['name'],
                    'status' => $row['status'],
                    'why' => $row['why'],
                    'conditions' => $row['conditions'] ?? null,
                    'notes' => $row['notes'] ?? null,
                    'aliases' => $row['aliases'] ?? [],
                    'access' => $row['access'] ?? 'public',
                ],
            );
        }
    }

    private function seedAllergies(): void
    {
        $products = Product::query()->with('category')->get();

        foreach ($this->json('allergies.json') as $index => $row) {
            $allergy = Allergy::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'name' => $row['name'],
                    'short' => $row['short'] ?? null,
                    'intro' => $row['intro'],
                    'is_main' => (bool) ($row['is_main'] ?? false),
                    'sort_order' => $index,
                ],
            );

            $categorySlugs = $row['categories'] ?? [];
            $productSlugs = $row['slugs'] ?? [];

            $ids = $products
                ->filter(function (Product $product) use ($categorySlugs, $productSlugs) {
                    $categorySlug = $product->category?->slug;

                    return in_array($product->slug, $productSlugs, true)
                        || ($categorySlug !== null && in_array($categorySlug, $categorySlugs, true));
                })
                ->pluck('id')
                ->all();

            $allergy->products()->sync($ids);
        }
    }

    private function seedGuides(): void
    {
        foreach ($this->json('guides.json') as $index => $row) {
            $guide = Guide::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title' => $row['title'],
                    'summary' => $row['summary'],
                    'access' => $row['access'] ?? 'public',
                    'sort_order' => $index,
                ],
            );

            $guide->sections()->delete();
            foreach ($row['sections'] ?? [] as $sectionIndex => $section) {
                GuideSection::query()->create([
                    'guide_id' => $guide->id,
                    'heading' => $section['heading'] ?? null,
                    'body' => $section['body'],
                    'sort_order' => $sectionIndex,
                ]);
            }
        }
    }

    private function seedMenu(): void
    {
        $payload = $this->json('menu.json');
        $products = Product::query()->pluck('id', 'slug');

        DB::table('menu_meal_product')->delete();
        MenuMeal::query()->delete();
        MenuDay::query()->delete();

        foreach ($payload['days'] as $dayRow) {
            $day = MenuDay::query()->create([
                'day' => $dayRow['day'],
                'weekday' => $dayRow['weekday'],
                'week' => $dayRow['week'],
            ]);

            foreach ($dayRow['meals'] as $mealIndex => $mealRow) {
                $meal = MenuMeal::query()->create([
                    'menu_day_id' => $day->id,
                    'label' => $mealRow['label'],
                    'text' => $mealRow['text'],
                    'note' => $mealRow['note'] ?? null,
                    'sort_order' => $mealIndex,
                ]);
                $ids = collect($mealRow['productSlugs'] ?? [])
                    ->map(fn (string $slug) => $products[$slug] ?? null)
                    ->filter()
                    ->all();
                $meal->products()->sync($ids);
            }
        }
    }

    private function json(string $file): array
    {
        $path = database_path('data/'.$file);
        $decoded = json_decode((string) file_get_contents($path), true);

        return is_array($decoded) ? $decoded : [];
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\ProductStatus;
use App\Models\Allergy;
use App\Models\Category;
use App\Models\Guide;
use App\Models\MenuDay;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KnowledgeController extends Controller
{
    public function home(): View
    {
        $categories = Category::query()->with('products')->orderBy('sort_order')->get();
        $allergies = Allergy::query()->with('products')->orderBy('sort_order')->get();
        $guides = Guide::query()->orderBy('sort_order')->get();

        return view('home', [
            'categories' => $categories,
            'allergies' => $allergies,
            'guides' => $guides,
            'productCount' => Product::query()->count(),
        ]);
    }

    public function search(Request $request): View
    {
        return view('search', [
            'query' => (string) $request->string('q'),
        ]);
    }

    public function categories(): View
    {
        return view('categories.index', [
            'categories' => Category::query()->with('products')->orderBy('sort_order')->get(),
        ]);
    }

    public function category(Category $category): View
    {
        $products = $category->products()->orderBy('name')->get()->groupBy(fn (Product $product) => $product->status->value);

        return view('categories.show', [
            'category' => $category,
            'grouped' => $products,
            'order' => ProductStatus::cases(),
        ]);
    }

    public function allergies(): View
    {
        return view('allergies.index', [
            'allergies' => Allergy::query()->with('products')->orderBy('sort_order')->get(),
        ]);
    }

    public function allergy(Allergy $allergy): View
    {
        $products = $allergy->products()->with('category')->orderBy('name')->get()
            ->groupBy(fn (Product $product) => $product->status->value);

        return view('allergies.show', [
            'allergy' => $allergy,
            'grouped' => $products,
            'order' => ProductStatus::cases(),
        ]);
    }

    public function product(Product $product): View
    {
        $product->load(['category', 'allergies']);

        return view('products.show', [
            'product' => $product,
            'related' => $product->related(),
        ]);
    }

    public function menu(): View
    {
        $days = MenuDay::query()->with(['meals.products'])->orderBy('day')->get();

        return view('menu', ['days' => $days]);
    }

    public function guides(): View
    {
        return view('guides.index', [
            'guides' => Guide::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function guide(Guide $guide): View
    {
        $guide->load('sections');

        return view('guides.show', [
            'guide' => $guide,
            'others' => Guide::query()->where('id', '!=', $guide->id)->orderBy('sort_order')->get(),
        ]);
    }

    public function suggestions(Request $request): JsonResponse
    {
        $q = trim((string) $request->string('q'));
        $kind = (string) $request->string('kind');
        if (mb_strlen($q) < 1) {
            return response()->json(['suggestions' => []]);
        }

        $like = '%'.$q.'%';
        $products = Product::query()
            ->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('aliases', 'like', $like);
            })
            ->limit(6)
            ->get()
            ->map(fn (Product $product) => [
                'kind' => 'product',
                'title' => $product->name,
                'slug' => $product->slug,
                'href' => route('products.show', $product->slug),
                'status' => $product->status->value,
                'status_label' => $product->status->label(),
                'status_class' => $product->status->badgeClass(),
                'hint' => null,
            ]);

        if ($kind === 'product') {
            return response()->json(['suggestions' => $products->values()]);
        }

        $categories = Category::query()
            ->where('name', 'like', $like)
            ->orWhere('short', 'like', $like)
            ->limit(2)
            ->get()
            ->map(fn (Category $category) => [
                'kind' => 'category',
                'title' => $category->name,
                'href' => route('categories.show', $category->slug),
                'hint' => 'Categorie',
            ]);

        $guides = Guide::query()
            ->where('title', 'like', $like)
            ->orWhere('summary', 'like', $like)
            ->limit(2)
            ->get()
            ->map(fn (Guide $guide) => [
                'kind' => 'guide',
                'title' => $guide->title,
                'href' => route('guides.show', $guide->slug),
                'hint' => 'Gids',
            ]);

        $allergies = Allergy::query()
            ->where('name', 'like', $like)
            ->orWhere('short', 'like', $like)
            ->limit(2)
            ->get()
            ->map(fn (Allergy $allergy) => [
                'kind' => 'allergy',
                'title' => $allergy->name,
                'href' => route('allergies.show', $allergy->slug),
                'hint' => 'Allergie',
            ]);

        return response()->json([
            'suggestions' => $products->concat($categories)->concat($allergies)->concat($guides)->values(),
        ]);
    }

    public function results(Request $request): JsonResponse
    {
        $q = trim((string) $request->string('q'));
        $status = (string) $request->string('status', 'alles');

        $query = Product::query()->with('category');
        if ($q !== '') {
            $query->search($q);
        }
        if ($status !== '' && $status !== 'alles') {
            $query->where('status', $status);
        }

        $products = $query->orderBy('name')->limit(120)->get()->map(fn (Product $product) => [
            'name' => $product->name,
            'slug' => $product->slug,
            'href' => route('products.show', $product->slug),
            'why' => $product->why,
            'status' => $product->status->value,
            'status_label' => $product->status->label(),
            'status_class' => $product->status->badgeClass(),
        ]);

        return response()->json([
            'count' => $products->count(),
            'products' => $products,
        ]);
    }
}

<?php

use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\UnmatchedQueryController;
use App\Models\Allergy;
use App\Models\Category;
use App\Models\Guide;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::bind('allergy', fn (string $value) => Allergy::query()->where('slug', $value)->firstOrFail());
Route::bind('category', fn (string $value) => Category::query()->where('slug', $value)->firstOrFail());
Route::bind('product', fn (string $value) => Product::query()->where('slug', $value)->firstOrFail());
Route::bind('guide', fn (string $value) => Guide::query()->where('slug', $value)->firstOrFail());

Route::get('/', [KnowledgeController::class, 'home'])->name('home');
Route::get('/zoeken', [KnowledgeController::class, 'search'])->name('search');
Route::get('/zoeken/suggesties', [KnowledgeController::class, 'suggestions'])->name('search.suggestions');
Route::get('/zoeken/resultaten', [KnowledgeController::class, 'results'])->name('search.results');
Route::post('/zoeken/gemist', [UnmatchedQueryController::class, 'store'])->name('search.unmatched');

Route::get('/categorieen', [KnowledgeController::class, 'categories'])->name('categories.index');
Route::get('/categorieen/{category}', [KnowledgeController::class, 'category'])->name('categories.show');
Route::get('/allergieen', [KnowledgeController::class, 'allergies'])->name('allergies.index');
Route::get('/allergieen/{allergy}', [KnowledgeController::class, 'allergy'])->name('allergies.show');
Route::get('/product/{product}', [KnowledgeController::class, 'product'])->name('products.show');
Route::get('/menu', [KnowledgeController::class, 'menu'])->name('menu');
Route::get('/gids', [KnowledgeController::class, 'guides'])->name('guides.index');
Route::get('/gids/{guide}', [KnowledgeController::class, 'guide'])->name('guides.show');

<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiaryController;
use App\Http\Controllers\KnowledgeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\PlusController;
use App\Http\Controllers\SymptomGuideController;
use App\Http\Controllers\UnmatchedQueryController;
use App\Models\Allergy;
use App\Models\Category;
use App\Models\Guide;
use App\Models\Product;
use App\Models\SymptomGuide;
use Illuminate\Support\Facades\Route;

Route::bind('allergy', fn (string $value) => Allergy::query()->where('slug', $value)->firstOrFail());
Route::bind('category', fn (string $value) => Category::query()->where('slug', $value)->firstOrFail());
Route::bind('product', fn (string $value) => Product::query()->where('slug', $value)->firstOrFail());
Route::bind('guide', fn (string $value) => Guide::query()->where('slug', $value)->firstOrFail());
Route::bind('symptom', fn (string $value) => SymptomGuide::query()->where('slug', $value)->firstOrFail());

Route::get('/', LandingController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/inloggen', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/inloggen', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/account', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/account', [AuthController::class, 'register'])->middleware('throttle:5,1');
});

Route::post('/uitloggen', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::post('/mollie/webhook', [PlusController::class, 'webhook'])->name('mollie.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/kennisbank', [KnowledgeController::class, 'home'])->name('kennisbank');
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
    Route::get('/assistent', [AssistantController::class, 'create'])->name('assistant');
    Route::post('/assistent', [AssistantController::class, 'store'])->middleware('throttle:10,1')->name('assistant.store');
    Route::get('/gids', [KnowledgeController::class, 'guides'])->name('guides.index');
    Route::get('/gids/{guide}', [KnowledgeController::class, 'guide'])->name('guides.show');

    Route::get('/wat-nu', [SymptomGuideController::class, 'index'])->name('symptoms.index');
    Route::get('/wat-nu/{symptom}', [SymptomGuideController::class, 'show'])->name('symptoms.show');

    Route::get('/plus', [PlusController::class, 'index'])->name('plus.index');
    Route::post('/plus/checkout', [PlusController::class, 'checkoutPlus'])->middleware('throttle:10,1')->name('plus.checkout');
    Route::get('/plus/terug/{payment}', [PlusController::class, 'returnPlus'])->name('plus.return');
    Route::post('/doneren', [PlusController::class, 'checkoutDonate'])->middleware('throttle:10,1')->name('donate.checkout');
    Route::get('/doneren/bedankt/{payment}', [PlusController::class, 'thanksDonate'])->name('donate.thanks');

    Route::middleware('plus')->group(function () {
        Route::get('/dagboek', [DiaryController::class, 'index'])->name('diary.index');
        Route::post('/dagboek', [DiaryController::class, 'store'])->name('diary.store');
        Route::put('/dagboek/{diary}', [DiaryController::class, 'update'])->name('diary.update');
        Route::delete('/dagboek/{diary}', [DiaryController::class, 'destroy'])->name('diary.destroy');
    });
});

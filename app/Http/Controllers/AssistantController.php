<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateFridgeMenuRequest;
use App\Models\SavedMenu;
use App\Services\AssistantUsageLimiter;
use App\Services\FridgeMenuGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class AssistantController extends Controller
{
    public function create(Request $request, AssistantUsageLimiter $limiter): View
    {
        $user = $request->user();
        $savedId = $request->integer('saved');
        $menu = null;

        if ($savedId > 0) {
            $saved = SavedMenu::query()
                ->where('user_id', $user->id)
                ->whereKey($savedId)
                ->first();

            if ($saved) {
                $menu = $saved->payload;
                $menu['saved_id'] = $saved->id;
            }
        }

        return view('assistant', [
            'menu' => $menu,
            'configured' => $this->configured(),
            'remaining' => $limiter->remaining($user),
            'isPlus' => $user->isPlus(),
            'savedMenus' => SavedMenu::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(12)
                ->get(),
            'dailyLimit' => AssistantUsageLimiter::FREE_DAILY_LIMIT,
        ]);
    }

    public function store(
        GenerateFridgeMenuRequest $request,
        FridgeMenuGenerator $generator,
        AssistantUsageLimiter $limiter,
    ): View|RedirectResponse {
        $user = $request->user();

        if (! $this->configured()) {
            return back()
                ->withInput()
                ->withErrors(['one' => 'De AI-assistent is nog niet geconfigureerd.']);
        }

        if (! $limiter->canGenerate($user)) {
            return back()
                ->withInput()
                ->withErrors(['one' => 'Je hebt vandaag al '.AssistantUsageLimiter::FREE_DAILY_LIMIT.' menu\'s gemaakt. Upgrade naar Plus voor onbeperkt gebruik.']);
        }

        try {
            $menu = $generator->generate(
                $request->string('one')->toString(),
                $request->string('two')->toString(),
                $request->string('one_slug')->toString() ?: null,
                $request->string('two_slug')->toString() ?: null,
            );
        } catch (RuntimeException $exception) {
            return back()
                ->withInput()
                ->withErrors(['one' => $exception->getMessage()]);
        }

        $limiter->hit($user);

        $products = collect($menu['products'] ?? [])
            ->map(fn (array $row) => [
                'name' => $row['name'] ?? '',
                'slug' => $row['product']->slug ?? null,
                'status' => $row['product']?->status?->value,
            ])
            ->all();

        $storable = $menu;
        $storable['products'] = collect($menu['products'] ?? [])
            ->map(fn (array $row) => [
                'name' => $row['name'] ?? '',
                'slug' => $row['product']->slug ?? null,
                'status' => $row['product']?->status?->value,
                'status_label' => $row['product']?->status?->label(),
                'product' => null,
            ])
            ->all();

        $saved = SavedMenu::query()->create([
            'user_id' => $user->id,
            'title' => $menu['title'] ?? 'Menu',
            'products' => $products,
            'payload' => $storable,
        ]);

        $menu['saved_id'] = $saved->id;

        return view('assistant', [
            'menu' => $menu,
            'configured' => true,
            'remaining' => $limiter->remaining($user),
            'isPlus' => $user->isPlus(),
            'savedMenus' => SavedMenu::query()
                ->where('user_id', $user->id)
                ->latest()
                ->limit(12)
                ->get(),
            'dailyLimit' => AssistantUsageLimiter::FREE_DAILY_LIMIT,
        ]);
    }

    private function configured(): bool
    {
        return filled(config('services.openrouter.key'));
    }
}

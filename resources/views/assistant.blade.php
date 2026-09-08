@extends('layouts.app')

@section('title', 'Koelkast-assistent')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">AI-assistent</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Wat ligt er in je koelkast?</h1>
    <p class="mt-6 max-w-2xl text-sm leading-relaxed text-muted">
        Noem twee producten. De assistent maakt daar een dagmenu van dat past bij het anti-candidadieet, met de kennisbank als leidraad.
    </p>

    @if (! $configured)
        <p class="mt-6 rounded-[20px] border border-line bg-limited-soft px-4 py-3 text-sm text-limited">
            De assistent is nog niet gekoppeld. Voeg een OpenRouter-sleutel toe in de omgevingsvariabelen.
        </p>
    @endif

    @if (! $isPlus)
        <p class="mt-6 rounded-[20px] border border-line bg-card px-4 py-3 text-sm text-muted">
            Gratis: {{ $dailyLimit }} menu's per dag.
            @if ($remaining !== null)
                Nog <strong class="text-ink">{{ $remaining }}</strong> over vandaag.
            @endif
            <a href="{{ route('plus.index') }}" class="font-medium text-accent hover:underline">Plus = onbeperkt</a>.
        </p>
    @else
        <p class="mt-6 text-sm text-muted">Plus: onbeperkte menu's. Opgeslagen menu's kun je hieronder hergebruiken.</p>
    @endif

    @if ($errors->any())
        <p class="mt-6 rounded-[20px] border border-line bg-deny-soft px-4 py-3 text-sm text-deny">
            {{ $errors->first() }}
        </p>
    @endif

    <form method="POST" action="{{ route('assistant.store') }}" class="mt-8 space-y-4" data-assistant-form>
        @csrf
        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ([['one', 'Eerste product'], ['two', 'Tweede product']] as [$name, $label])
                @php
                    $idx = $name === 'one' ? 0 : 1;
                    $productRow = $menu['products'][$idx] ?? null;
                    $oldName = old($name, is_array($productRow) ? ($productRow['name'] ?? '') : '');
                    $oldSlug = old($name.'_slug', is_array($productRow) ? ($productRow['product']->slug ?? $productRow['slug'] ?? '') : '');
                @endphp
                <div data-product-picker data-suggestions-url="{{ route('search.suggestions') }}">
                    <label for="{{ $name }}" class="text-xs font-medium uppercase tracking-[0.14em] text-muted">{{ $label }}</label>
                    <div class="relative mt-2">
                        <input
                            id="{{ $name }}"
                            name="{{ $name }}"
                            value="{{ $oldName }}"
                            required
                            maxlength="80"
                            autocomplete="off"
                            placeholder="Bijv. {{ $name === 'one' ? 'courgette' : 'ei' }}"
                            class="js-picker-input w-full rounded-[18px] border border-line bg-card px-4 py-3 text-sm text-ink outline-none ring-ink/10 placeholder:text-muted focus:ring-2"
                        >
                        <input type="hidden" name="{{ $name }}_slug" value="{{ $oldSlug }}" class="js-picker-slug">
                        <ul class="js-picker-suggest absolute z-20 mt-2 hidden w-full overflow-hidden rounded-[18px] border border-line bg-card shadow-sm"></ul>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="submit" class="js-assistant-submit rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:opacity-90 disabled:opacity-60">
            Maak een menu
        </button>
    </form>

    @if ($savedMenus->isNotEmpty())
        <section class="mt-10">
            <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Opgeslagen</p>
            <ul class="mt-3 space-y-2">
                @foreach ($savedMenus as $saved)
                    <li>
                        <a href="{{ route('assistant', ['saved' => $saved->id]) }}" class="flex items-center justify-between rounded-[18px] border border-line bg-card px-4 py-3 text-sm transition hover:border-ink/20">
                            <span class="font-medium text-ink">{{ $saved->title }}</span>
                            <span class="text-xs text-muted">{{ $saved->created_at->timezone(config('app.timezone'))->format('d-m H:i') }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    @if ($menu)
        <article class="mt-10 rounded-[24px] border border-line bg-card p-5 sm:p-6">
            <header>
                <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Jouw menu</p>
                <h2 class="mt-2 text-xl font-semibold tracking-tight">{{ $menu['title'] }}</h2>
                @if ($menu['intro'] ?? null)
                    <p class="mt-3 text-sm leading-relaxed text-muted">{{ $menu['intro'] }}</p>
                @endif
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach ($menu['products'] ?? [] as $item)
                        @php $product = $item['product'] ?? null; @endphp
                        @if ($product)
                            <a href="{{ route('products.show', $product->slug) }}" class="inline-flex items-center gap-2 rounded-full bg-paper px-2.5 py-1 text-xs font-medium text-ink ring-1 ring-line hover:ring-ink/20">
                                {{ $product->name }}
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $product->status->badgeClass() }}">{{ $product->status->label() }}</span>
                            </a>
                        @elseif (! empty($item['slug']))
                            <a href="{{ route('products.show', $item['slug']) }}" class="inline-flex items-center rounded-full bg-paper px-2.5 py-1 text-xs font-medium text-ink ring-1 ring-line hover:ring-ink/20">
                                {{ $item['name'] ?? $item['slug'] }}
                            </a>
                        @else
                            <span class="inline-flex items-center rounded-full bg-paper px-2.5 py-1 text-xs font-medium text-muted ring-1 ring-line">{{ $item['name'] ?? 'Product' }} · niet in kennisbank</span>
                        @endif
                    @endforeach
                </div>
            </header>
            @if ($menu['warning'] ?? null)
                <p class="mt-5 rounded-[16px] border border-line bg-limited-soft px-4 py-3 text-sm text-limited">{{ $menu['warning'] }}</p>
            @endif
            <ul class="mt-6 space-y-4">
                @foreach ($menu['meals'] ?? [] as $meal)
                    <li>
                        <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">{{ $meal['label'] }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-ink">{{ $meal['text'] }}</p>
                        @if ($meal['note'] ?? null)
                            <p class="mt-1 text-xs text-conditional">{{ $meal['note'] }}</p>
                        @endif
                    </li>
                @endforeach
            </ul>
            @if (! empty($menu['tips']))
                <ul class="mt-6 space-y-2 border-t border-line pt-5 text-sm leading-relaxed text-muted">
                    @foreach ($menu['tips'] as $tip)
                        <li>{{ $tip }}</li>
                    @endforeach
                </ul>
            @endif
        </article>
    @endif
</div>
@endsection

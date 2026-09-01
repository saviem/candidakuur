@extends('layouts.app')

@section('title', 'Voorbeeldmenu')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6" x-data="{ week: 1 }">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Anti-candidadieet</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Menu voor 3 weken</h1>
    <p class="mt-6 max-w-2xl text-sm leading-relaxed text-muted">
        Voorbeeldmenu van iemand die de kuur volgde. Week 3 is een herhaling van week 1. Kook vers; eet 4 tot 5 kleine porties.
    </p>
    <div class="mt-6 flex gap-2">
        @foreach ([1, 2, 3] as $week)
            <button type="button" data-week="{{ $week }}" class="js-week rounded-full px-4 py-2 text-sm font-medium {{ $week === 1 ? 'bg-ink text-paper' : 'bg-card text-muted ring-1 ring-line' }}">
                Week {{ $week }}
            </button>
        @endforeach
    </div>
    <p id="week-3-note" class="mt-4 hidden rounded-[20px] border border-line bg-accent-soft px-4 py-3 text-sm text-accent">
        Week 3 is een herhaling van week 1.
    </p>
    @foreach ($days as $day)
        <article data-week="{{ $day->week }}" class="js-day mt-6 rounded-[24px] border border-line bg-card p-5 sm:p-6 {{ $day->week !== 1 ? 'hidden' : '' }}">
            <header class="flex items-baseline justify-between gap-3">
                <h2 class="text-lg font-semibold tracking-tight">Dag {{ $day->day }}</h2>
                <p class="text-sm text-muted">{{ $day->weekday }}</p>
            </header>
            <ul class="mt-5 space-y-4">
                @foreach ($day->meals as $meal)
                    <li>
                        <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">{{ $meal->label }}</p>
                        <p class="mt-1 text-sm leading-relaxed text-ink">{{ $meal->text }}</p>
                        @if ($meal->note)
                            <p class="mt-1 text-xs text-conditional">{{ $meal->note }}</p>
                        @endif
                        @if ($meal->products->isNotEmpty())
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($meal->products as $product)
                                    <a href="{{ route('products.show', $product->slug) }}" class="inline-flex items-center gap-2 rounded-full bg-paper px-2.5 py-1 text-xs font-medium text-ink ring-1 ring-line hover:ring-ink/20">
                                        {{ $product->name }}
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $product->status->badgeClass() }}">{{ $product->status->label() }}</span>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>
        </article>
    @endforeach
</div>
<script>
    document.querySelectorAll('.js-week').forEach((button) => {
        button.addEventListener('click', () => {
            const week = Number(button.dataset.week);
            const show = week === 3 ? 1 : week;
            document.getElementById('week-3-note').classList.toggle('hidden', week !== 3);
            document.querySelectorAll('.js-week').forEach((b) => {
                b.className = b === button
                    ? 'js-week rounded-full px-4 py-2 text-sm font-medium bg-ink text-paper'
                    : 'js-week rounded-full px-4 py-2 text-sm font-medium bg-card text-muted ring-1 ring-line';
            });
            document.querySelectorAll('.js-day').forEach((day) => {
                day.classList.toggle('hidden', Number(day.dataset.week) !== show);
            });
        });
    });
</script>
@endsection

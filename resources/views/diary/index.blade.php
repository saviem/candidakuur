@extends('layouts.app')

@section('title', 'Dagboek')

@section('content')
@php
    $selectedKey = $selectedDate->toDateString();
    $isToday = $selectedDate->isToday();
    $heading = $entry
        ? ($isToday ? 'Vandaag bijwerken' : $selectedDate->translatedFormat('l j F').' bijwerken')
        : ($isToday ? 'Vandaag noteren' : $selectedDate->translatedFormat('l j F').' noteren');
@endphp
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">{{ $isPlus ? 'Plus' : 'Dagboek' }}</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Dagboek</h1>
    <p class="mt-4 text-muted">Houd stemming, energie en klachten bij. Zo zie je patronen, en krijgen Wat nu?-tips meer betekenis.</p>

    @if (session('status'))
        <p class="mt-6 rounded-[20px] border border-line bg-accent-soft px-4 py-3 text-sm text-accent">{{ session('status') }}</p>
    @endif

    @unless ($isPlus)
        <div class="mt-6 rounded-[20px] border border-line bg-accent-soft px-4 py-4 sm:px-5">
            <p class="text-sm font-medium text-accent">Invullen hoort bij Candidakuur Plus</p>
            <p class="mt-1 text-sm text-muted">Je kunt het dagboek al bekijken. Om dagen bij te houden heb je Plus nodig.</p>
            <a href="{{ route('plus.index') }}" class="mt-3 inline-flex rounded-full bg-ink px-4 py-2 text-sm font-medium text-paper transition hover:opacity-90">Bekijk Plus</a>
        </div>
    @endunless

    <div class="mt-8 flex items-center justify-between gap-3">
        <a href="{{ route('diary.index', ['week' => $weekStart->copy()->subWeek()->toDateString(), 'date' => $weekStart->copy()->subWeek()->toDateString()]) }}" class="text-sm font-medium text-accent hover:underline">&larr; Vorige week</a>
        <p class="text-sm text-muted">{{ $weekStart->translatedFormat('j M') }} - {{ $weekEnd->translatedFormat('j M Y') }}</p>
        <a href="{{ route('diary.index', ['week' => $weekStart->copy()->addWeek()->toDateString(), 'date' => $weekStart->copy()->addWeek()->min(now())->toDateString()]) }}" class="text-sm font-medium text-accent hover:underline">Volgende week &rarr;</a>
    </div>

    <div class="mt-6 grid gap-2 sm:grid-cols-7">
        @for ($d = $weekStart->copy(); $d <= $weekEnd; $d->addDay())
            @php
                $key = $d->toDateString();
                $dayEntry = $entries->get($key);
                $isFuture = $d->isAfter(now()->startOfDay());
                $isSelected = $key === $selectedKey;
            @endphp
            @if ($isFuture)
                <div class="rounded-[18px] border border-line bg-card/60 p-3 text-center opacity-50">
                    <p class="text-[10px] uppercase tracking-wide text-muted">{{ $d->translatedFormat('D') }}</p>
                    <p class="mt-1 text-sm font-semibold">{{ $d->format('j') }}</p>
                    <p class="mt-2 text-[11px] text-muted/60">-</p>
                </div>
            @else
                <a
                    href="{{ route('diary.index', ['date' => $key, 'week' => $weekStart->toDateString()]) }}"
                    class="rounded-[18px] border p-3 text-center transition {{ $isSelected ? 'border-accent bg-accent-soft ring-1 ring-accent/30' : 'border-line bg-card hover:border-accent/40' }}"
                >
                    <p class="text-[10px] uppercase tracking-wide text-muted">{{ $d->translatedFormat('D') }}</p>
                    <p class="mt-1 text-sm font-semibold">{{ $d->format('j') }}</p>
                    @if ($dayEntry)
                        <p class="mt-2 text-[11px] text-muted">🙂 {{ $dayEntry->mood }}/5</p>
                        <p class="text-[11px] text-muted">⚡ {{ $dayEntry->energy }}/5</p>
                    @else
                        <p class="mt-2 text-[11px] text-muted/60">invullen</p>
                    @endif
                </a>
            @endif
        @endfor
    </div>

    @if ($suggestions->isNotEmpty())
        <section class="mt-10 rounded-[24px] border border-line bg-card p-5">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Suggesties</p>
            <h2 class="mt-2 text-lg font-semibold tracking-tight">Tags die terugkomen, bekijk Wat nu?</h2>
            <ul class="mt-4 space-y-2">
                @foreach ($suggestions as $guide)
                    <li>
                        <a href="{{ route('symptoms.show', $guide->slug) }}" class="text-sm font-medium text-accent hover:underline">{{ $guide->title }}</a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <section class="mt-10 rounded-[24px] border border-line bg-card p-5 sm:p-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h2 class="text-lg font-semibold tracking-tight">{{ $heading }}</h2>
            <div class="flex gap-3 text-sm">
                <a href="{{ route('diary.index', ['date' => now()->subDay()->toDateString(), 'week' => now()->subDay()->startOfWeek()->toDateString()]) }}" class="font-medium text-accent hover:underline">Gisteren</a>
                @unless ($isToday)
                    <a href="{{ route('diary.index', ['date' => now()->toDateString()]) }}" class="font-medium text-accent hover:underline">Vandaag</a>
                @endunless
            </div>
        </div>
        <form method="POST" action="{{ $isPlus ? ($entry ? route('diary.update', $entry) : route('diary.store')) : '#' }}" class="mt-5 space-y-4 {{ $isPlus ? '' : 'pointer-events-none select-none opacity-60' }}" @unless($isPlus) aria-disabled="true" @endunless>
            @csrf
            @if ($entry && $isPlus)
                @method('PUT')
            @endif
            <input type="hidden" name="entry_date" value="{{ old('entry_date', $selectedKey) }}" @disabled(! $isPlus)>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Stemming (1-5)</p>
                    <div class="mt-2 flex flex-wrap gap-2" role="group" aria-label="Stemming">
                        @php $mood = (int) old('mood', $entry->mood ?? 3); @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="{{ $isPlus ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                                <input type="radio" name="mood" value="{{ $i }}" class="peer sr-only" @checked($mood === $i) @disabled(! $isPlus) @required($isPlus)>
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line text-sm font-medium transition peer-checked:border-accent peer-checked:bg-accent peer-checked:text-paper peer-focus-visible:ring-2 peer-focus-visible:ring-accent/40">{{ $i }}</span>
                            </label>
                        @endfor
                    </div>
                    @error('mood')
                        <p class="mt-2 text-sm text-deny">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Energie (1-5)</p>
                    <div class="mt-2 flex flex-wrap gap-2" role="group" aria-label="Energie">
                        @php $energy = (int) old('energy', $entry->energy ?? 3); @endphp
                        @for ($i = 1; $i <= 5; $i++)
                            <label class="{{ $isPlus ? 'cursor-pointer' : 'cursor-not-allowed' }}">
                                <input type="radio" name="energy" value="{{ $i }}" class="peer sr-only" @checked($energy === $i) @disabled(! $isPlus) @required($isPlus)>
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full border border-line text-sm font-medium transition peer-checked:border-accent peer-checked:bg-accent peer-checked:text-paper peer-focus-visible:ring-2 peer-focus-visible:ring-accent/40">{{ $i }}</span>
                            </label>
                        @endfor
                    </div>
                    @error('energy')
                        <p class="mt-2 text-sm text-deny">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Symptomen</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @php $selected = old('symptom_tags', $entry->symptom_tags ?? []); @endphp
                    @foreach ($symptomOptions as $tag)
                        <label class="inline-flex items-center gap-2 rounded-full border border-line px-3 py-1.5 text-xs {{ $isPlus ? '' : 'cursor-not-allowed' }}">
                            <input type="checkbox" name="symptom_tags[]" value="{{ $tag }}" @checked(in_array($tag, $selected, true)) @disabled(! $isPlus)>
                            {{ $tag }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Notitie</label>
                <textarea name="note" rows="3" class="mt-2 w-full rounded-[18px] border border-line bg-paper px-4 py-3 text-sm" placeholder="Hoe ging de dag?" @disabled(! $isPlus)>{{ old('note', $entry->note ?? '') }}</textarea>
            </div>

            <div>
                <label class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Maaltijden (optioneel)</label>
                <textarea name="meals" rows="3" class="mt-2 w-full rounded-[18px] border border-line bg-paper px-4 py-3 text-sm" placeholder="Kort wat je at" @disabled(! $isPlus)>{{ old('meals', $entry->meals ?? '') }}</textarea>
            </div>

            @if ($isPlus)
                <button type="submit" class="rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:opacity-90">Opslaan</button>
            @else
                <a href="{{ route('plus.index') }}" class="inline-flex rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:opacity-90">Activeer Plus om op te slaan</a>
            @endif
        </form>

        @if ($entry && $isPlus)
            <form method="POST" action="{{ route('diary.destroy', $entry) }}" class="mt-4" onsubmit="return confirm('Notitie verwijderen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-deny hover:underline">Verwijderen</button>
            </form>
        @endif
    </section>
</div>
@endsection

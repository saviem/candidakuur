@extends('layouts.app')

@section('title', 'Dagboek')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Plus</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Dagboek</h1>
    <p class="mt-4 text-muted">Houd stemming, energie en klachten bij. Zo zie je patronen — en krijgen Wat nu?-tips meer betekenis.</p>

    @if (session('status'))
        <p class="mt-6 rounded-[20px] border border-line bg-accent-soft px-4 py-3 text-sm text-accent">{{ session('status') }}</p>
    @endif

    <div class="mt-8 flex items-center justify-between gap-3">
        <a href="{{ route('diary.index', ['week' => $weekStart->copy()->subWeek()->toDateString()]) }}" class="text-sm font-medium text-accent hover:underline">← Vorige week</a>
        <p class="text-sm text-muted">{{ $weekStart->translatedFormat('j M') }} – {{ $weekEnd->translatedFormat('j M Y') }}</p>
        <a href="{{ route('diary.index', ['week' => $weekStart->copy()->addWeek()->toDateString()]) }}" class="text-sm font-medium text-accent hover:underline">Volgende week →</a>
    </div>

    <div class="mt-6 grid gap-2 sm:grid-cols-7">
        @for ($d = $weekStart->copy(); $d <= $weekEnd; $d->addDay())
            @php $key = $d->toDateString(); $entry = $entries->get($key); @endphp
            <div class="rounded-[18px] border border-line bg-card p-3 text-center">
                <p class="text-[10px] uppercase tracking-wide text-muted">{{ $d->translatedFormat('D') }}</p>
                <p class="mt-1 text-sm font-semibold">{{ $d->format('j') }}</p>
                @if ($entry)
                    <p class="mt-2 text-[11px] text-muted">🙂 {{ $entry->mood }}/5</p>
                    <p class="text-[11px] text-muted">⚡ {{ $entry->energy }}/5</p>
                @else
                    <p class="mt-2 text-[11px] text-muted/60">—</p>
                @endif
            </div>
        @endfor
    </div>

    @if ($suggestions->isNotEmpty())
        <section class="mt-10 rounded-[24px] border border-line bg-card p-5">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Suggesties</p>
            <h2 class="mt-2 text-lg font-semibold tracking-tight">Tags die terugkomen — bekijk Wat nu?</h2>
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
        <h2 class="text-lg font-semibold tracking-tight">{{ $today ? 'Vandaag bijwerken' : 'Vandaag noteren' }}</h2>
        <form method="POST" action="{{ $today ? route('diary.update', $today) : route('diary.store') }}" class="mt-5 space-y-4">
            @csrf
            @if ($today)
                @method('PUT')
            @endif
            <input type="hidden" name="entry_date" value="{{ old('entry_date', optional($today)->entry_date?->toDateString() ?? now()->toDateString()) }}">

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Stemming (1–5)</label>
                    <select name="mood" class="mt-2 w-full rounded-[18px] border border-line bg-paper px-4 py-3 text-sm" required>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" @selected((int) old('mood', $today->mood ?? 3) === $i)>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Energie (1–5)</label>
                    <select name="energy" class="mt-2 w-full rounded-[18px] border border-line bg-paper px-4 py-3 text-sm" required>
                        @for ($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" @selected((int) old('energy', $today->energy ?? 3) === $i)>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div>
                <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Symptomen</p>
                <div class="mt-2 flex flex-wrap gap-2">
                    @php $selected = old('symptom_tags', $today->symptom_tags ?? []); @endphp
                    @foreach ($symptomOptions as $tag)
                        <label class="inline-flex items-center gap-2 rounded-full border border-line px-3 py-1.5 text-xs">
                            <input type="checkbox" name="symptom_tags[]" value="{{ $tag }}" @checked(in_array($tag, $selected, true))>
                            {{ $tag }}
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Notitie</label>
                <textarea name="note" rows="3" class="mt-2 w-full rounded-[18px] border border-line bg-paper px-4 py-3 text-sm" placeholder="Hoe ging de dag?">{{ old('note', $today->note ?? '') }}</textarea>
            </div>

            <div>
                <label class="text-xs font-medium uppercase tracking-[0.14em] text-muted">Maaltijden (optioneel)</label>
                <textarea name="meals" rows="3" class="mt-2 w-full rounded-[18px] border border-line bg-paper px-4 py-3 text-sm" placeholder="Kort wat je at">{{ old('meals', $today->meals ?? '') }}</textarea>
            </div>

            <button type="submit" class="rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:opacity-90">Opslaan</button>
        </form>

        @if ($today)
            <form method="POST" action="{{ route('diary.destroy', $today) }}" class="mt-4" onsubmit="return confirm('Notitie verwijderen?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-sm text-deny hover:underline">Verwijderen</button>
            </form>
        @endif
    </section>
</div>
@endsection

@extends('layouts.app')

@section('title', $isPlus ? 'Gefeliciteerd, Plus is actief' : 'Plus betaling')

@section('content')
<div class="mx-auto max-w-2xl px-4 py-16 sm:px-6">
    @if ($isPlus)
        <div class="overflow-hidden rounded-[28px] border border-line bg-card shadow-sm">
            <div class="bg-accent-soft px-6 py-10 text-center sm:px-10">
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Candidakuur Plus</p>
                <p class="mt-4 text-4xl" aria-hidden="true">🎉</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Gefeliciteerd</h1>
                <p class="mx-auto mt-4 max-w-md text-base leading-relaxed text-muted">
                    Plus is actief. Je hebt nu de tools die de kuur makkelijker maken, elke dag.
                </p>
                @if ($plusUntil)
                    <p class="mt-3 text-sm text-muted">
                        Actief tot {{ $plusUntil->timezone(config('app.timezone'))->format('d-m-Y') }}.
                    </p>
                @endif
            </div>

            <div class="px-6 py-8 sm:px-10">
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Dit kun je nu</p>
                <ul class="mt-5 space-y-4">
                    <li class="flex gap-4 rounded-[20px] border border-line bg-paper p-4">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent-soft text-sm font-semibold text-accent">1</span>
                        <div>
                            <p class="font-semibold tracking-tight">Koelkast-assistent</p>
                            <p class="mt-1 text-sm leading-relaxed text-muted">Noem wat je in huis hebt en krijg een kuur-proof dagmenu.</p>
                        </div>
                    </li>
                    <li class="flex gap-4 rounded-[20px] border border-line bg-paper p-4">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent-soft text-sm font-semibold text-accent">2</span>
                        <div>
                            <p class="font-semibold tracking-tight">Dagboek</p>
                            <p class="mt-1 text-sm leading-relaxed text-muted">Houd stemming, energie en klachten bij, zie patronen over de week.</p>
                        </div>
                    </li>
                    <li class="flex gap-4 rounded-[20px] border border-line bg-paper p-4">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-accent-soft text-sm font-semibold text-accent">3</span>
                        <div>
                            <p class="font-semibold tracking-tight">Volledige Wat nu?-kaarten</p>
                            <p class="mt-1 text-sm leading-relaxed text-muted">Concrete tips bij hoofdpijn, moeheid, cravings en meer.</p>
                        </div>
                    </li>
                </ul>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <a href="{{ route('assistant') }}" class="inline-flex items-center justify-center rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:opacity-90">Open de assistent</a>
                    <a href="{{ route('diary.index') }}" class="inline-flex items-center justify-center rounded-full border border-line bg-paper px-5 py-2.5 text-sm font-medium text-ink transition hover:border-accent/40">Naar het dagboek</a>
                    <a href="{{ route('symptoms.index') }}" class="inline-flex items-center justify-center rounded-full border border-line bg-paper px-5 py-2.5 text-sm font-medium text-ink transition hover:border-accent/40">Wat nu?</a>
                </div>
            </div>
        </div>
    @else
        <div class="rounded-[28px] border border-line bg-card px-6 py-12 text-center sm:px-10">
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Candidakuur Plus</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight">Betaling wordt bevestigd</h1>
            <p class="mx-auto mt-4 max-w-md text-muted">
                Status: <strong class="text-ink">{{ $payment->status }}</strong>.
                De bevestiging via Mollie kan even duren. Vernieuw deze pagina zo nodig, daarna zetten we de slingers uit.
            </p>
            <a href="{{ url()->current() }}" class="mt-8 inline-flex rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper">Vernieuwen</a>
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Candidakuur Plus')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Candidakuur</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Plus</h1>
    <p class="mt-4 text-muted">
        Onbeperkte assistent, dagboek, en volledige Wat nu?-kaarten op candidakuur.nl. Donaties houden de kennisbank beschikbaar, die ontgrendelen Plus niet.
    </p>

    @if (session('status'))
        <p class="mt-6 rounded-[20px] border border-line bg-accent-soft px-4 py-3 text-sm text-accent">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <p class="mt-6 rounded-[20px] border border-line bg-deny-soft px-4 py-3 text-sm text-deny">{{ $errors->first() }}</p>
    @endif

    @if ($isPlus)
        <div class="mt-8 rounded-[24px] border border-line bg-accent-soft p-6">
            <p class="text-sm font-semibold text-accent">Je hebt Plus</p>
            <p class="mt-2 text-sm text-muted">Actief tot {{ $plusUntil?->timezone(config('app.timezone'))->format('d-m-Y H:i') }}.</p>
        </div>
    @else
        <div class="mt-8 rounded-[24px] border border-line bg-card p-6">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Abonnement</p>
            <h2 class="mt-2 text-2xl font-semibold tracking-tight">€ {{ number_format((float) $plusPrice, 2, ',', '.') }}</h2>
            <p class="mt-2 text-sm text-muted">Voor 30 dagen Plus via Mollie (iDEAL e.d.).</p>
            <ul class="mt-5 space-y-2 text-sm text-ink">
                <li>• Onbeperkte koelkast-assistent</li>
                <li>• Dagboek met weekoverzicht</li>
                <li>• Volledige Wat nu?-kaarten</li>
            </ul>

            @if ($configured)
                <form method="POST" action="{{ route('plus.checkout') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:opacity-90">
                        Activeer Plus
                    </button>
                </form>
            @else
                <p class="mt-6 rounded-[18px] border border-line bg-limited-soft px-4 py-3 text-sm text-limited">
                    Betalingen zijn nog niet geconfigureerd. Voeg <code class="text-xs">MOLLIE_KEY</code> toe aan je omgeving.
                </p>
            @endif
        </div>
    @endif

    <div class="mt-8 rounded-[24px] border border-line bg-card p-6">
        <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Steun</p>
        <h2 class="mt-2 text-xl font-semibold tracking-tight">Doneer</h2>
        <p class="mt-2 text-sm text-muted">Een eenmalige bijdrage. Dit ontgrendelt Plus niet.</p>

        @if ($configured)
            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($donateAmounts as $amount)
                    <form method="POST" action="{{ route('donate.checkout') }}">
                        @csrf
                        <input type="hidden" name="amount" value="{{ $amount }}">
                        <button type="submit" class="rounded-full border border-line bg-paper px-4 py-2 text-sm font-medium transition hover:border-ink/20">
                            € {{ $amount }}
                        </button>
                    </form>
                @endforeach
            </div>
        @else
            <p class="mt-5 text-sm text-limited">Doneren vereist ook een Mollie-sleutel.</p>
        @endif
    </div>
</div>
@endsection

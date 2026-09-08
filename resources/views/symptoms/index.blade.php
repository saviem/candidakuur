@extends('layouts.app')

@section('title', 'Wat nu?')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Dieet &amp; bijwerkingen</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Wat nu?</h1>
    <p class="mt-4 text-muted">Korte kaarten bij veelvoorkomende klachten tijdens de kuur. Geen diagnose — wel praktische houvast.</p>

    <p class="mt-6 rounded-[20px] border border-line bg-limited-soft px-4 py-3 text-sm text-limited">
        Medische disclaimer: dit is voedingsadvies van Candidakuur / praktijk ARDRA, geen vervanging van een arts. Bij rode vlaggen of twijfel: bel de praktijk of huisarts.
    </p>

    @unless ($isPlus)
        <p class="mt-4 rounded-[20px] border border-line bg-card px-4 py-3 text-sm text-muted">
            Je ziet de samenvattingen gratis. Volledige tips zitten in
            <a href="{{ route('plus.index') }}" class="font-medium text-accent hover:underline">Candidakuur Plus</a>.
        </p>
    @endunless

    <ul class="mt-10 space-y-3">
        @foreach ($guides as $guide)
            <li>
                <a href="{{ route('symptoms.show', $guide->slug) }}" class="block rounded-[20px] border border-line bg-card p-5 transition hover:border-ink/20">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="font-semibold tracking-tight">{{ $guide->title }}</h2>
                        @unless ($isPlus)
                            <span class="rounded-full bg-accent-soft px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-accent">Plus</span>
                        @endunless
                    </div>
                    <p class="mt-1 text-sm text-muted">{{ $guide->summary }}</p>
                    @if ($guide->tags)
                        <div class="mt-3 flex flex-wrap gap-1.5">
                            @foreach ($guide->tags as $tag)
                                <span class="rounded-full border border-line px-2 py-0.5 text-[11px] text-muted">{{ $tag }}</span>
                            @endforeach
                        </div>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection

@extends('layouts.app')

@section('title', $guide->title)

@section('content')
<article class="mx-auto max-w-2xl px-4 py-12 sm:px-6">
    <a href="{{ route('symptoms.index') }}" class="text-sm font-medium text-accent hover:underline">← Wat nu?</a>
    <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $guide->title }}</h1>
    <p class="mt-3 text-muted">{{ $guide->summary }}</p>

    <p class="mt-6 rounded-[20px] border border-line bg-limited-soft px-4 py-3 text-sm text-limited">
        Medische disclaimer: dit vervangt geen medisch advies. Bij ernstige of aanhoudende klachten: praktijk ARDRA of huisarts.
    </p>

    @if ($isPlus)
        <div class="mt-10 space-y-8">
            @if ($guide->body_common)
                <section>
                    <h2 class="text-lg font-semibold tracking-tight">Komt vaker voor</h2>
                    <p class="mt-3 text-base leading-relaxed text-ink">{{ $guide->body_common }}</p>
                </section>
            @endif
            @if ($guide->body_practical)
                <section>
                    <h2 class="text-lg font-semibold tracking-tight">Praktische hulp</h2>
                    <p class="mt-3 text-base leading-relaxed text-ink">{{ $guide->body_practical }}</p>
                </section>
            @endif
            @if ($guide->body_contact)
                <section>
                    <h2 class="text-lg font-semibold tracking-tight">Wanneer contact opnemen</h2>
                    <p class="mt-3 text-base leading-relaxed text-ink">{{ $guide->body_contact }}</p>
                </section>
            @endif
        </div>
    @else
        <div class="mt-10 rounded-[24px] border border-line bg-card p-6">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Teaser</p>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                De volledige uitleg, wat normaal is, wat je zelf kunt doen, en wanneer je belt, zit in Candidakuur Plus.
            </p>
            <a href="{{ route('plus.index') }}" class="mt-5 inline-flex rounded-full bg-ink px-5 py-2.5 text-sm font-medium text-paper transition hover:opacity-90">
                Bekijk Plus
            </a>
        </div>
    @endif

    <nav class="mt-16 border-t border-line pt-8">
        <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Andere kaarten</p>
        <ul class="mt-4 space-y-2">
            @foreach ($others as $item)
                <li>
                    <a href="{{ route('symptoms.show', $item->slug) }}" class="text-sm font-medium text-accent hover:underline">{{ $item->title }}</a>
                </li>
            @endforeach
        </ul>
    </nav>
</article>
@endsection

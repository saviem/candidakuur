@extends('layouts.app')

@section('title', 'Gids')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Protocol</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">De volledige gids</h1>
    <p class="mt-4 text-muted">Achtergrond bij de kennisbank: waarom de kuur, candida, nystatine, roulatie, vitaminen en darmflora.</p>
    <ul class="mt-10 space-y-3">
        @foreach ($guides as $guide)
            <li>
                <a href="{{ route('guides.show', $guide->slug) }}" class="block rounded-[20px] border border-line bg-card p-5 transition hover:border-ink/20">
                    <h2 class="font-semibold tracking-tight">{{ $guide->title }}</h2>
                    <p class="mt-1 text-sm text-muted">{{ $guide->summary }}</p>
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection

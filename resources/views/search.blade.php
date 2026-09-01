@extends('layouts.app')

@section('title', 'Zoeken')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Zoeken</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Wat mag ik eten?</h1>
    <p class="mt-3 max-w-xl text-muted">Typ een product. Filter op wel, niet, beperkt of voorwaardelijk.</p>
    <div class="mt-8">
        @include('partials.search-panel', ['compact' => false, 'initial' => $query])
    </div>
</div>
@endsection

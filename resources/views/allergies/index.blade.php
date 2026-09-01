@extends('layouts.app')

@section('title', 'Allergieën')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Kennisbank</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Producten per allergie</h1>
    <p class="mt-3 max-w-2xl text-muted">
        De drie hoofdallergenen van het dieet, plus ei en histamine. Per allergie zie je welke producten in de kennisbank horen, en of ze tijdens de kuur wel of niet mogen.
    </p>
    <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($allergies as $allergy)
            @include('partials.allergy-card', ['allergy' => $allergy])
        @endforeach
    </div>
</div>
@endsection

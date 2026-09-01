@extends('layouts.app')

@section('title', 'Categorieën')

@section('content')
<div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Kennisbank</p>
    <h1 class="mt-3 text-3xl font-semibold tracking-tight sm:text-4xl">Alle categorieën</h1>
    <p class="mt-3 max-w-xl text-muted">Per groep wat wel en niet mag, met de uitleg uit het voedingsadvies.</p>
    <div class="mt-10 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($categories as $category)
            @include('partials.category-card', ['category' => $category])
        @endforeach
    </div>
</div>
@endsection

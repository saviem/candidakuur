@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="mx-auto max-w-3xl px-4 py-12 sm:px-6">
    <a href="{{ route('categories.show', $product->category->slug) }}" class="text-sm font-medium text-accent hover:underline">← {{ $product->category->name }}</a>
    <div class="mt-4 flex flex-wrap items-center gap-3">
        <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">{{ $product->name }}</h1>
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide {{ $product->status->badgeClass() }}">
            {{ $product->status->label() }}
        </span>
    </div>
    @if ($product->aliases)
        <p class="mt-3 text-sm text-muted">Ook bekend als {{ implode(', ', $product->aliases) }}</p>
    @endif
    @if ($product->allergies->isNotEmpty())
        <div class="mt-4 flex flex-wrap gap-2">
            @foreach ($product->allergies as $allergy)
                <a href="{{ route('allergies.show', $allergy->slug) }}" class="rounded-full bg-card px-3 py-1 text-xs font-medium text-muted ring-1 ring-line hover:text-ink">
                    {{ $allergy->name }}
                </a>
            @endforeach
        </div>
    @endif
    <section class="mt-8 rounded-[24px] border border-line bg-card p-6 sm:p-8">
        <h2 class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Waarom</h2>
        <p class="mt-3 text-base leading-relaxed text-ink">{{ $product->why }}</p>
        @if ($product->conditions)
            <p class="mt-4 rounded-2xl bg-conditional-soft px-4 py-3 text-sm leading-relaxed text-conditional">{{ $product->conditions }}</p>
        @endif
        @if ($product->notes)
            <p class="mt-3 text-sm leading-relaxed text-muted">{{ $product->notes }}</p>
        @endif
    </section>
    @if ($related->isNotEmpty())
        <section class="mt-12">
            <h2 class="text-lg font-semibold tracking-tight">Gerelateerd</h2>
            <div class="mt-4 grid gap-3">
                @foreach ($related as $item)
                    @include('partials.product-card', ['product' => $item])
                @endforeach
            </div>
        </section>
    @endif
</div>
@endsection

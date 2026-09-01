@extends('layouts.app')

@section('title', $allergy->name)

@section('content')
<div class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <a href="{{ route('allergies.index') }}" class="text-sm font-medium text-accent hover:underline">← Allergieën</a>
    <p class="mt-4 text-xs font-medium uppercase tracking-[0.16em] text-muted">{{ $allergy->short }}</p>
    <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $allergy->name }}</h1>
    <p class="mt-5 max-w-3xl text-base leading-relaxed text-muted">{{ $allergy->intro }}</p>
    <div class="mt-12 space-y-12">
        @foreach ($order as $status)
            @php $items = $grouped->get($status->value, collect()); @endphp
            @if ($items->isNotEmpty())
                <section>
                    <h2 class="text-lg font-semibold tracking-tight">
                        {{ $status->label() }}
                        <span class="ml-2 text-sm font-normal text-muted">{{ $items->count() }}</span>
                    </h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach ($items as $product)
                            @include('partials.product-card', ['product' => $product])
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    </div>
</div>
@endsection

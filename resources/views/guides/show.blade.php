@extends('layouts.app')

@section('title', $guide->title)

@section('content')
<article class="mx-auto max-w-2xl px-4 py-12 sm:px-6">
    <a href="{{ route('guides.index') }}" class="text-sm font-medium text-accent hover:underline">← Gids</a>
    <h1 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">{{ $guide->title }}</h1>
    <p class="mt-3 text-muted">{{ $guide->summary }}</p>
    <div class="mt-10 space-y-8">
        @foreach ($guide->sections as $section)
            <section>
                @if ($section->heading)
                    <h2 class="text-lg font-semibold tracking-tight">{{ $section->heading }}</h2>
                @endif
                <p class="text-base leading-relaxed text-ink {{ $section->heading ? 'mt-3' : '' }}">{{ $section->body }}</p>
            </section>
        @endforeach
    </div>
    <nav class="mt-16 border-t border-line pt-8">
        <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Verder lezen</p>
        <ul class="mt-4 space-y-2">
            @foreach ($others as $item)
                <li>
                    <a href="{{ route('guides.show', $item->slug) }}" class="text-sm font-medium text-accent hover:underline">{{ $item->title }}</a>
                </li>
            @endforeach
        </ul>
    </nav>
</article>
@endsection

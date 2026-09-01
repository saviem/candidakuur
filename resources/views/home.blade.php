@extends('layouts.app')

@section('title', 'Voedingsadvies')

@section('content')
<section class="mx-auto max-w-6xl px-4 pb-8 pt-14 sm:px-6 sm:pt-20">
    <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Kennisbank · 3 weken kuur</p>
    <h1 class="mt-4 max-w-3xl text-4xl font-semibold tracking-tight text-ink sm:text-6xl sm:leading-[1.05]">Mag ik dit eten?</h1>
    <p class="mt-5 max-w-xl text-lg leading-relaxed text-muted">
        Zoek een product, zie meteen of het mag tijdens het dieet, en waarom. Inclusief voorbeeldmenu en de volledige gids van praktijk ARDRA.
    </p>
    <div class="mt-10 max-w-2xl">
        @include('partials.search-panel', ['compact' => true])
    </div>
    <div class="mt-8 flex flex-wrap gap-6 text-sm text-muted">
        <span><strong class="text-ink">{{ $productCount }}</strong> producten</span>
        <span><strong class="text-ink">{{ $categories->count() }}</strong> categorieën</span>
        <span><strong class="text-ink">{{ $allergies->count() }}</strong> allergieën</span>
        <span><strong class="text-ink">3</strong> weken menu</span>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <div class="mb-6 flex items-end justify-between">
        <h2 class="text-2xl font-semibold tracking-tight">Categorieën</h2>
        <a href="{{ route('categories.index') }}" class="text-sm font-medium text-accent hover:underline">Alles</a>
    </div>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($categories as $category)
            @include('partials.category-card', ['category' => $category])
        @endforeach
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-12 sm:px-6">
    <div class="mb-6 flex items-end justify-between">
        <h2 class="text-2xl font-semibold tracking-tight">Allergieën</h2>
        <a href="{{ route('allergies.index') }}" class="text-sm font-medium text-accent hover:underline">Alles</a>
    </div>
    <p class="mb-6 max-w-2xl text-sm leading-relaxed text-muted">
        Gluten, zuivel en snelle suikers zijn de hoofdallergenen van het dieet. Ei en histamine komen daar als subgroep bij.
    </p>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @foreach ($allergies as $allergy)
            @include('partials.allergy-card', ['allergy' => $allergy])
        @endforeach
    </div>
</section>

<section class="mx-auto grid max-w-6xl gap-3 px-4 py-12 sm:px-6 lg:grid-cols-2">
    <a href="{{ route('menu') }}" class="rounded-[24px] bg-ink p-8 text-paper transition hover:opacity-95">
        <p class="text-xs font-medium uppercase tracking-[0.16em] text-paper/60">Voorbeeld</p>
        <h2 class="mt-3 text-2xl font-semibold tracking-tight">Menu voor 3 weken</h2>
        <p class="mt-3 max-w-md text-sm leading-relaxed text-paper/70">
            Ontbijt, lunch, diner en snacks per dag. Tik op een product voor de toelichting.
        </p>
    </a>
    <div class="rounded-[24px] border border-line bg-card p-8">
        <p class="text-xs font-medium uppercase tracking-[0.16em] text-muted">Gids</p>
        <h2 class="mt-3 text-2xl font-semibold tracking-tight">Waarom het werkt</h2>
        <ul class="mt-5 space-y-2">
            @foreach ($guides->take(5) as $guide)
                <li>
                    <a href="{{ route('guides.show', $guide->slug) }}" class="text-sm font-medium text-accent hover:underline">{{ $guide->title }}</a>
                    <span class="ml-2 text-sm text-muted">{{ $guide->summary }}</span>
                </li>
            @endforeach
        </ul>
        <a href="{{ route('guides.index') }}" class="mt-6 inline-block text-sm font-medium text-ink hover:text-accent">Alle artikelen →</a>
    </div>
</section>
@endsection

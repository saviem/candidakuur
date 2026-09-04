@php
    $home = auth()->check() ? route('kennisbank') : route('home');
    $links = auth()->check()
        ? [
            ['route' => 'search', 'label' => 'Zoeken'],
            ['route' => 'categories.index', 'label' => 'Categorieën'],
            ['route' => 'allergies.index', 'label' => 'Allergieën'],
            ['route' => 'menu', 'label' => 'Menu'],
            ['route' => 'guides.index', 'label' => 'Gids'],
        ]
        : [];
@endphp

<header class="sticky top-0 z-40 border-b border-line/80 bg-paper/80 backdrop-blur-md">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ $home }}" class="flex items-baseline gap-2">
            <span class="text-lg font-semibold tracking-tight text-ink">Voedingsadvies</span>
            <span class="text-[11px] font-medium uppercase tracking-[0.16em] text-muted">ARDRA</span>
        </a>
        <nav class="hidden items-center gap-1 md:flex">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="rounded-full px-3 py-1.5 text-sm font-medium text-muted transition hover:bg-card hover:text-ink">
                    {{ $link['label'] }}
                </a>
            @endforeach
            @auth
                <a href="{{ route('search') }}" class="ml-2 hidden items-center gap-2 rounded-full border border-line bg-card px-3 py-1.5 text-xs text-muted lg:flex">
                    Zoeken
                    <kbd class="rounded bg-paper px-1.5 py-0.5 font-sans text-[10px] text-muted">⌘K</kbd>
                </a>
                @if (auth()->user()->is_admin)
                    <a href="{{ url('/admin') }}" class="rounded-full px-3 py-1.5 text-sm font-medium text-muted transition hover:bg-card hover:text-ink">Admin</a>
                @endif
                <form method="POST" action="{{ url('/uitloggen') }}" class="ml-1">
                    @csrf
                    <button type="submit" class="rounded-full px-3 py-1.5 text-sm font-medium text-muted transition hover:bg-card hover:text-ink">Uitloggen</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="rounded-full px-3 py-1.5 text-sm font-medium text-muted transition hover:bg-card hover:text-ink">Inloggen</a>
                <a href="{{ route('register') }}" class="rounded-full bg-ink px-3.5 py-1.5 text-sm font-medium text-paper transition hover:opacity-90">Account</a>
            @endauth
        </nav>
        <button type="button" class="rounded-full border border-line bg-card px-3 py-1.5 text-sm font-medium md:hidden" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')">
            Menu
        </button>
    </div>
    <nav id="mobile-nav" class="hidden border-t border-line bg-paper px-4 py-3 md:hidden">
        @foreach ($links as $link)
            <a href="{{ route($link['route']) }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink hover:bg-card">
                {{ $link['label'] }}
            </a>
        @endforeach
        @auth
            @if (auth()->user()->is_admin)
                <a href="{{ url('/admin') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink hover:bg-card">Admin</a>
            @endif
            <form method="POST" action="{{ url('/uitloggen') }}">
                @csrf
                <button type="submit" class="block w-full rounded-xl px-3 py-2 text-left text-sm font-medium text-ink hover:bg-card">Uitloggen</button>
            </form>
        @else
            <a href="{{ route('login') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink hover:bg-card">Inloggen</a>
            <a href="{{ route('register') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink hover:bg-card">Account aanmaken</a>
        @endauth
    </nav>
</header>

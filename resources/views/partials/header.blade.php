@php
    $home = auth()->check() ? route('kennisbank') : route('home');
    $links = auth()->check()
        ? [
            ['route' => 'assistant', 'label' => 'Menu'],
            ['route' => 'diary.index', 'label' => 'Dagboek'],
            ['route' => 'symptoms.index', 'label' => 'Wat nu?'],
            ['route' => 'guides.index', 'label' => 'Gids'],
        ]
        : [];
@endphp

<header class="sticky top-0 z-40 border-b border-line/80 bg-paper/80 backdrop-blur-md">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3 sm:px-6">
        <a href="{{ $home }}" class="flex items-baseline gap-2">
            <span class="text-lg font-semibold tracking-tight text-ink">Candidakuur</span>
            <span class="text-[11px] font-medium uppercase tracking-[0.16em] text-muted">Anti-candida</span>
        </a>
        <nav class="hidden items-center gap-1 md:flex">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}" class="rounded-full px-3 py-1.5 text-sm font-medium text-muted transition hover:bg-card hover:text-ink">
                    {{ $link['label'] }}
                </a>
            @endforeach
            @auth
                @unless (auth()->user()->isPlus())
                    <a href="{{ route('plus.index') }}" class="ml-1 rounded-full bg-accent-soft px-3 py-1.5 text-sm font-medium text-accent transition hover:opacity-90">Plus</a>
                @endunless
                <a href="{{ route('search') }}" class="ml-2 hidden items-center gap-2 rounded-full border border-line bg-card px-3 py-1.5 text-xs text-muted lg:flex">
                    Zoeken
                    <kbd class="rounded bg-paper px-1.5 py-0.5 font-sans text-[10px] text-muted">⌘K</kbd>
                </a>
                <details class="relative ml-1">
                    <summary class="flex cursor-pointer list-none items-center rounded-full border border-line bg-card p-1.5 text-muted transition hover:bg-paper hover:text-ink" aria-label="Accountmenu">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" class="h-5 w-5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 7.5a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 19.5a7.5 7.5 0 0 1 15 0" />
                        </svg>
                    </summary>
                    <div class="absolute right-0 mt-2 w-48 overflow-hidden rounded-2xl border border-line bg-card py-1 shadow-lg">
                        @if (auth()->user()->is_admin)
                            <a href="{{ url('/admin') }}" class="block px-4 py-2.5 text-sm font-medium text-ink hover:bg-paper">Admin</a>
                        @endif
                        <a href="{{ route('account.edit') }}" class="block px-4 py-2.5 text-sm font-medium text-ink hover:bg-paper">Mijn account</a>
                        <form method="POST" action="{{ url('/uitloggen') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2.5 text-left text-sm font-medium text-ink hover:bg-paper">Uitloggen</button>
                        </form>
                    </div>
                </details>
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
            <a href="{{ route('search') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink hover:bg-card">Zoeken</a>
            @unless (auth()->user()->isPlus())
                <a href="{{ route('plus.index') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-accent hover:bg-card">Plus</a>
            @endunless
            @if (auth()->user()->is_admin)
                <a href="{{ url('/admin') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink hover:bg-card">Admin</a>
            @endif
            <a href="{{ route('account.edit') }}" class="block rounded-xl px-3 py-2 text-sm font-medium text-ink hover:bg-card">Mijn account</a>
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

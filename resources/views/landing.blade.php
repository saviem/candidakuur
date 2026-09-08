@extends('layouts.app')

@section('description', 'Het anti-candidadieet van praktijk ARDRA in Haarlem. Zoek wat je mag eten tijdens de drie weken kuur, met uitleg, menu en gids.')

@section('content')
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute -right-24 -top-24 h-80 w-80 rounded-full bg-accent-soft"></div>
    <div class="pointer-events-none absolute -left-16 top-64 h-56 w-56 rounded-full bg-conditional-soft"></div>
    <div class="relative mx-auto max-w-6xl px-4 pb-16 pt-16 sm:px-6 sm:pb-24 sm:pt-24">
        <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Praktijk ARDRA · Haarlem</p>
        <h1 class="mt-5 max-w-3xl text-4xl font-semibold tracking-tight text-ink sm:text-6xl sm:leading-[1.05]">
            Weet je straks precies wat je mag eten.
        </h1>
        <p class="mt-6 max-w-xl text-lg leading-relaxed text-muted">
            De kennisbank bij het anti-candidadieet van HP. H.A. Stormer. Zoek een product, zie of het mag tijdens de drie weken, en waarom.
        </p>
        <div class="mt-10 flex flex-wrap items-center gap-3">
            @auth
                <a href="{{ route('kennisbank') }}" class="rounded-full bg-ink px-6 py-3 text-sm font-semibold text-paper transition hover:opacity-90">Naar de kennisbank</a>
            @else
                <a href="{{ route('register') }}" class="rounded-full bg-ink px-6 py-3 text-sm font-semibold text-paper transition hover:opacity-90">Account aanmaken</a>
                <a href="{{ route('login') }}" class="rounded-full border border-line bg-card px-6 py-3 text-sm font-semibold text-ink transition hover:border-ink/20">Inloggen</a>
            @endauth
        </div>
        <div class="mt-8 max-w-xl">
            @include('partials.install-app')
        </div>
        <div class="mt-12 flex flex-wrap gap-8 text-sm text-muted">
            <span><strong class="text-2xl font-semibold tracking-tight text-ink">{{ $productCount }}</strong><br>producten</span>
            <span><strong class="text-2xl font-semibold tracking-tight text-ink">{{ $categoryCount }}</strong><br>categorieën</span>
            <span><strong class="text-2xl font-semibold tracking-tight text-ink">3</strong><br>weken menu</span>
            <span><strong class="text-2xl font-semibold tracking-tight text-ink">{{ $guideCount }}</strong><br>gidsartikelen</span>
        </div>
    </div>
</section>

<section class="border-y border-line bg-card">
    <div class="mx-auto grid max-w-6xl gap-0 px-4 sm:px-6 lg:grid-cols-3">
        <div class="border-b border-line px-2 py-10 lg:border-b-0 lg:border-r lg:px-8 lg:py-14">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-accent">01</p>
            <h2 class="mt-3 text-xl font-semibold tracking-tight">Zoek een product</h2>
            <p class="mt-3 text-sm leading-relaxed text-muted">Tomaat, brood, olijfolie, kaas. Je ziet meteen of het mag, beperkt is, of tijdens de kuur beter blijft staan.</p>
        </div>
        <div class="border-b border-line px-2 py-10 lg:border-b-0 lg:border-r lg:px-8 lg:py-14">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-accent">02</p>
            <h2 class="mt-3 text-xl font-semibold tracking-tight">Volg het menu</h2>
            <p class="mt-3 text-sm leading-relaxed text-muted">Een voorbeeld voor drie weken: ontbijt, lunch, diner en snacks. Tik door naar de toelichting bij elk product.</p>
        </div>
        <div class="px-2 py-10 lg:px-8 lg:py-14">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-accent">03</p>
            <h2 class="mt-3 text-xl font-semibold tracking-tight">Lees waarom</h2>
            <p class="mt-3 text-sm leading-relaxed text-muted">De gids legt candida, nystatine, allergieën en de kuur uit, in de woorden van de praktijk.</p>
        </div>
    </div>
</section>

<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid items-center gap-10 lg:grid-cols-2">
        <div>
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Voor cliënten</p>
            <h2 class="mt-4 text-3xl font-semibold tracking-tight sm:text-4xl">Alles op één plek, achter je eigen account.</h2>
            <p class="mt-5 text-base leading-relaxed text-muted">
                De kennisbank is bedoeld als begeleiding bij de kuur. Na het inloggen zoek je rustig na wat op jouw bord mag — thuis, in de winkel, of als je ergens eet.
            </p>
            <ul class="mt-8 space-y-3 text-sm text-ink">
                <li class="flex gap-3"><span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-accent"></span>Producten per categorie en per allergie</li>
                <li class="flex gap-3"><span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-accent"></span>Toegelaten, beperkt, voorwaardelijk of niet</li>
                <li class="flex gap-3"><span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-accent"></span>{{ $allergyCount }} allergenen, waaronder gluten, zuivel en snelle suikers</li>
            </ul>
        </div>
        <div class="rounded-[28px] bg-ink p-8 text-paper sm:p-10">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-paper/50">Praktijk</p>
            <p class="mt-4 text-2xl font-semibold tracking-tight">HP. H.A. Stormer</p>
            <p class="mt-2 text-sm leading-relaxed text-paper/70">Praktijk voor Biologische Geneeswijzen. Houtmanpad 8 B 3, Haarlem.</p>
            <div class="mt-8 flex flex-wrap gap-4 text-sm">
                <a class="text-paper underline decoration-paper/30 underline-offset-4 hover:decoration-paper" href="tel:+31235441122">023 544 1122</a>
                <a class="text-paper underline decoration-paper/30 underline-offset-4 hover:decoration-paper" href="mailto:pvbg@ardra.nl">pvbg@ardra.nl</a>
            </div>
        </div>
    </div>
</section>

<section class="border-t border-line bg-accent-soft/60">
    <div class="mx-auto max-w-6xl px-4 py-16 text-center sm:px-6">
        <h2 class="text-3xl font-semibold tracking-tight">Klaar voor de kuur?</h2>
        <p class="mx-auto mt-4 max-w-lg text-muted">Maak een account of log in. Daarna staat de hele kennisbank voor je open.</p>
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            @auth
                <a href="{{ route('kennisbank') }}" class="rounded-full bg-ink px-6 py-3 text-sm font-semibold text-paper transition hover:opacity-90">Open de kennisbank</a>
            @else
                <a href="{{ route('register') }}" class="rounded-full bg-ink px-6 py-3 text-sm font-semibold text-paper transition hover:opacity-90">Account aanmaken</a>
                <a href="{{ route('login') }}" class="rounded-full border border-line bg-card px-6 py-3 text-sm font-semibold text-ink">Ik heb al een account</a>
            @endauth
        </div>
    </div>
</section>
@endsection

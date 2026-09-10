@extends('layouts.app')

@section('description', 'Heb je last van moeheid, suikertrek of een opgeblazen gevoel? Doe drie weken de candidakuur, met kennisbank, menu en begeleiding van praktijk ARDRA in Haarlem.')

@section('content')
{{-- Premium conversie-landing: symptomen → kuur → bewijs → product --}}

{{-- HERO --}}
<section class="relative overflow-hidden">
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_at_top_right,_var(--color-accent-soft)_0%,_transparent_55%)]"></div>
    <div class="pointer-events-none absolute -left-20 bottom-0 h-72 w-72 rounded-full bg-conditional-soft/70 blur-2xl"></div>
    <div class="relative mx-auto max-w-6xl px-4 pb-16 pt-14 sm:px-6 sm:pb-24 sm:pt-20">
        <p class="text-xs font-medium uppercase tracking-[0.22em] text-accent">3 weken · Candidakuur</p>
        <h1 class="mt-5 max-w-3xl text-4xl font-semibold tracking-tight text-ink sm:text-6xl sm:leading-[1.05]">
            Voel je weer helder.<br class="hidden sm:block">
            <span class="text-accent">Drie weken de kuur.</span>
        </h1>
        <p class="mt-6 max-w-xl text-lg leading-relaxed text-muted">
            De candidakuur helpt je lichaam tot rust te komen: minder suikertrek, meer energie, een rustiger buik.
            Met Candidakuur weet je precies wat er wél op je bord mag, elke dag.
        </p>
        <div class="mt-10 flex flex-wrap items-center gap-4">
            @auth
                <a href="{{ route('kennisbank') }}" class="rounded-full bg-ink px-8 py-4 text-sm font-semibold text-paper shadow-sm transition hover:opacity-90">Start in de kennisbank</a>
                <a href="{{ route('plus.index') }}" class="rounded-full border border-line bg-card px-8 py-4 text-sm font-semibold text-ink transition hover:border-ink/20">Bekijk Plus</a>
            @else
                <a href="{{ route('register') }}" class="rounded-full bg-ink px-8 py-4 text-sm font-semibold text-paper shadow-sm transition hover:opacity-90">Begin met de kuur</a>
                <a href="{{ route('login') }}" class="rounded-full border border-line bg-card px-8 py-4 text-sm font-semibold text-ink transition hover:border-ink/20">Ik heb al een account</a>
            @endauth
        </div>
        <p class="mt-5 text-sm text-muted">Gratis account · kennisbank + 3-wekenmenu · praktijk ARDRA Haarlem</p>
        <div class="mt-8 max-w-xl">
            @include('partials.install-app')
        </div>
        <div class="mt-14 grid grid-cols-2 gap-6 sm:flex sm:flex-wrap sm:gap-10">
            <div>
                <p class="text-3xl font-semibold tracking-tight text-ink">{{ $productCount }}+</p>
                <p class="mt-1 text-sm text-muted">producten nagekeken</p>
            </div>
            <div>
                <p class="text-3xl font-semibold tracking-tight text-ink">3</p>
                <p class="mt-1 text-sm text-muted">weken duidelijk menu</p>
            </div>
            <div>
                <p class="text-3xl font-semibold tracking-tight text-ink">{{ $guideCount }}</p>
                <p class="mt-1 text-sm text-muted">gidsartikelen</p>
            </div>
            <div>
                <p class="text-3xl font-semibold tracking-tight text-ink">{{ $allergyCount }}</p>
                <p class="mt-1 text-sm text-muted">allergenen in beeld</p>
            </div>
        </div>
    </div>
</section>

{{-- SYMPTOOM → KUUR --}}
<section class="border-y border-line bg-card">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
        <div class="max-w-2xl">
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Herkent u dit?</p>
            <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">Last van dit? Doe dan drie weken de kuur.</h2>
            <p class="mt-4 text-base leading-relaxed text-muted">
                Candida-overgroei en een overprikkeld spijsverteringsstelsel uiten zich vaak zo. De kuur is geen snelle truc: het is drie weken strak, helder eten, zodat je lichaam weer kan herstellen.
            </p>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @php
                $pathways = [
                    ['title' => 'Extreme moeheid', 'body' => 'Wordt u niet uitgerust wakker, ook na een volle nacht? Doe dan drie weken de kuur, veel mensen voelen hun energie terugkomen.'],
                    ['title' => 'Suikertrek & cravings', 'body' => 'Heeft u steeds trek in zoet of koolhydraten? De kuur doorbreekt die cyclus met een helder eetpatroon.'],
                    ['title' => 'Opgeblazen of onrustige buik', 'body' => 'Last van een opgeblazen gevoel, wisselende ontlasting of een zware maag? De kuur geeft uw darmen rust.'],
                    ['title' => 'Hoofdpijn of wisselende focus', 'body' => 'Hoofdpijn, mistig hoofd, moeite met concentratie? Veel mensen merken dat ze helderder worden tijdens de drie weken.'],
                    ['title' => 'Huid of jeuk', 'body' => 'Heeft u last van huidklachten of jeuk die maar aanhoudt? De kuur ondersteunt herstel van binnenuit.'],
                    ['title' => 'Het lijkt steeds erger', 'body' => 'Klachten die terugkomen of juist erger voelen? Dat is een signaal om de kuur serieus te starten, met begeleiding van de praktijk.'],
                ];
            @endphp
            @foreach ($pathways as $item)
                <article class="group rounded-3xl border border-line bg-paper p-8 transition hover:border-accent/30 hover:shadow-[0_12px_40px_-24px_rgba(15,23,20,0.35)]">
                    <h3 class="text-lg font-semibold tracking-tight text-ink">{{ $item['title'] }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-muted">{{ $item['body'] }}</p>
                    <p class="mt-5 text-xs font-medium uppercase tracking-[0.14em] text-accent">→ 3 weken de kuur</p>
                </article>
            @endforeach
        </div>
        <div class="mt-10">
            @auth
                <a href="{{ route('kennisbank') }}" class="inline-flex rounded-full bg-ink px-8 py-4 text-sm font-semibold text-paper transition hover:opacity-90">Ja, ik start de kennisbank</a>
            @else
                <a href="{{ route('register') }}" class="inline-flex rounded-full bg-ink px-8 py-4 text-sm font-semibold text-paper transition hover:opacity-90">Ja, ik wil de kuur doen</a>
            @endauth
        </div>
    </div>
</section>

{{-- BELOFTE / WAAROM --}}
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid items-start gap-12 lg:grid-cols-2">
        <div>
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Waarom de kuur werkt</p>
            <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">Mensen voelen zich echt gezonder.</h2>
            <p class="mt-5 text-base leading-relaxed text-muted">
                Niet omdat een app magie doet, maar omdat u drie weken weet wat u wél mag eten, en wat beter blijft staan.
                Minder giswerk. Minder valse starts. Meer rust in lichaam én hoofd.
            </p>
            <ul class="mt-8 space-y-4 text-sm text-ink">
                <li class="flex gap-3"><span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent"></span>Strak kader: toegelaten, beperkt, voorwaardelijk of niet</li>
                <li class="flex gap-3"><span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent"></span>Voorbeeldmenu voor alle drie de weken</li>
                <li class="flex gap-3"><span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent"></span>Uitleg in de woorden van praktijk ARDRA</li>
                <li class="flex gap-3"><span class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-accent"></span>Plus: assistent, dagboek en “Wat nu?” bij klachten</li>
            </ul>
        </div>
        <div class="rounded-[28px] border border-line bg-ink p-8 text-paper sm:p-10">
            <p class="text-xs font-medium uppercase tracking-[0.16em] text-paper/45">Uit de praktijk</p>
            <p class="mt-5 text-2xl font-semibold tracking-tight sm:text-3xl leading-snug">
                “De kuur is vooral even inkomen. Als je weet wat er mag, zeker met de app, is het goed te doen.”
            </p>
            <p class="mt-8 text-sm leading-relaxed text-paper/65">
                Candidakuur is de digitale kennisbank bij de candidakuur van HP. H.A. Stormer, 
                praktijk voor biologische geneeswijzen in Haarlem.
            </p>
            <div class="mt-8 flex flex-wrap gap-4 text-sm">
                <a class="text-paper underline decoration-paper/30 underline-offset-4 hover:decoration-paper" href="tel:+31235441122">023 544 1122</a>
                <a class="text-paper underline decoration-paper/30 underline-offset-4 hover:decoration-paper" href="mailto:pvbg@ardra.nl">pvbg@ardra.nl</a>
                <a class="text-paper underline decoration-paper/30 underline-offset-4 hover:decoration-paper" href="mailto:hallo@candidakuur.nl">hallo@candidakuur.nl</a>
            </div>
        </div>
    </div>
</section>

{{-- SAVIEM ERVARING (echt) --}}
<section class="border-y border-line bg-accent-soft/50">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
        <div class="grid gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Persoonlijke ervaring</p>
                <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">Ik doe de kuur elk jaar opnieuw, en het blijft goed.</h2>
                <p class="mt-5 text-base leading-relaxed text-muted">
                    Ik, Saviem, doe de candidakuur elk jaar opnieuw. Niet eenmalig, maar als terugkerende reset. Even inkomen, en daarna, zeker met de app, goed te doen.
                    De duidelijkheid over wat mag en wat niet maakt het verschil: minder twijfel in de winkel, rustiger eten, stabielere energie.
                </p>
                <p class="mt-4 text-base leading-relaxed text-muted">
                    Daarom bouwde ik Candidakuur: zodat u diezelfde helderheid heeft, elke dag, in uw zak.
                </p>
            </div>
            <figure class="rounded-[28px] border border-line bg-card p-8 shadow-[0_20px_60px_-40px_rgba(15,23,20,0.45)]">
                <blockquote class="text-lg font-medium leading-relaxed tracking-tight text-ink">
                    “Ik doe de kuur elk jaar opnieuw. Even inkomen, en met de app is het goed te doen, vooral omdat ik precies weet wat er wél mag.”
                </blockquote>
                <figcaption class="mt-6 flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-full bg-ink text-sm font-semibold text-paper">SJ</span>
                    <div>
                        <p class="text-sm font-semibold text-ink">Saviem Jansen</p>
                        <p class="text-xs text-muted">Maker van Candidakuur · doet de kuur elk jaar</p>
                    </div>
                </figcaption>
            </figure>
        </div>
    </div>
</section>

{{-- TESTIMONIALS, VOORBEELD (vervang door echte quotes) --}}
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    {{-- VOORBEELD, vervang door echte testimonials wanneer beschikbaar --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div class="max-w-xl">
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Ervaringen</p>
            <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">Wat mensen merken tijdens de kuur</h2>
        </div>
        <p class="rounded-full bg-limited-soft px-3 py-1 text-[11px] font-medium uppercase tracking-wide text-limited">Voorbeeldquotes · te vervangen</p>
    </div>
    <div class="mt-12 grid gap-8 lg:grid-cols-3">
        @php
            // VOORBEELD, vervang door echte quotes
            $examples = [
                [
                    'quote' => 'Na twee weken was mijn suikertrek echt minder. Even inkomen, daarna gaf het menu houvast, met de app was het goed te doen.',
                    'name' => 'Marieke',
                    'meta' => 'Haarlem · voorbeeld',
                ],
                [
                    'quote' => 'Ik wist nooit wat ik mocht eten. Nu kijk ik iets op en ga door. Minder stress in de supermarkt.',
                    'name' => 'Thomas',
                    'meta' => 'Amsterdam · voorbeeld',
                ],
                [
                    'quote' => 'De moeheid zat diep. Tegen week drie voelde ik me helderder en rustiger in mijn buik, eenmaal erin was het goed te doen.',
                    'name' => 'Linda',
                    'meta' => 'Noord-Holland · voorbeeld',
                ],
            ];
        @endphp
        @foreach ($examples as $t)
            <figure class="flex h-full flex-col rounded-3xl border border-line bg-card p-8">
                <blockquote class="flex-1 text-base leading-relaxed text-ink">“{{ $t['quote'] }}”</blockquote>
                <figcaption class="mt-6 border-t border-line pt-5">
                    <p class="text-sm font-semibold text-ink">{{ $t['name'] }}</p>
                    <p class="text-xs text-muted">{{ $t['meta'] }}</p>
                </figcaption>
            </figure>
        @endforeach
    </div>
</section>

{{-- HOE HET WERKT --}}
<section class="border-y border-line bg-card">
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
        <div class="max-w-2xl">
            <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">Hoe het werkt</p>
            <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">Van twijfel naar een helder bord.</h2>
        </div>
        <ol class="mt-12 grid gap-8 lg:grid-cols-3">
            <li class="rounded-3xl border border-line bg-paper p-8">
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-accent">01</p>
                <h3 class="mt-3 text-xl font-semibold tracking-tight">Maak een account</h3>
                <p class="mt-3 text-sm leading-relaxed text-muted">Gratis starten. Direct toegang tot de kennisbank, het 3-wekenmenu en de gids van de praktijk.</p>
            </li>
            <li class="rounded-3xl border border-line bg-paper p-8">
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-accent">02</p>
                <h3 class="mt-3 text-xl font-semibold tracking-tight">Zoek wat u mag eten</h3>
                <p class="mt-3 text-sm leading-relaxed text-muted">{{ $productCount }}+ producten, {{ $categoryCount }} categorieën. Toegelaten, beperkt, of beter laten staan, met uitleg.</p>
            </li>
            <li class="rounded-3xl border border-line bg-paper p-8">
                <p class="text-xs font-medium uppercase tracking-[0.16em] text-accent">03</p>
                <h3 class="mt-3 text-xl font-semibold tracking-tight">Houd de drie weken vol</h3>
                <p class="mt-3 text-sm leading-relaxed text-muted">Volg het menu, lees de gids, en upgrade naar Plus voor assistent, dagboek en Wat nu?-kaarten.</p>
            </li>
        </ol>
    </div>
</section>

{{-- FEATURES GRID (beknopt) --}}
<section class="mx-auto max-w-6xl px-4 py-16 sm:px-6 sm:py-20">
    <div class="max-w-2xl">
        <p class="text-xs font-medium uppercase tracking-[0.2em] text-accent">In Candidakuur</p>
        <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">Alles wat u nodig heeft om de kuur vol te houden.</h2>
    </div>
    <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="rounded-3xl border border-line p-8">
            <h3 class="font-semibold tracking-tight">Productzoeken</h3>
            <p class="mt-2 text-sm leading-relaxed text-muted">Tomaat, brood, olijfolie, kaas, meteen duidelijk of het mag.</p>
        </div>
        <div class="rounded-3xl border border-line p-8">
            <h3 class="font-semibold tracking-tight">3-wekenmenu</h3>
            <p class="mt-2 text-sm leading-relaxed text-muted">Ontbijt, lunch, diner en snacks. Tik door naar elk product.</p>
        </div>
        <div class="rounded-3xl border border-line p-8">
            <h3 class="font-semibold tracking-tight">Gids & uitleg</h3>
            <p class="mt-2 text-sm leading-relaxed text-muted">Candida, nystatine, allergieën, in de woorden van de praktijk.</p>
        </div>
        <div class="rounded-3xl border border-line p-8">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold tracking-tight">Koelkast-assistent</h3>
                <span class="rounded-full bg-accent-soft px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-accent">Plus</span>
            </div>
            <p class="mt-2 text-sm leading-relaxed text-muted">Noem wat u in huis heeft; krijg een kuur-proof dagmenu.</p>
        </div>
        <div class="rounded-3xl border border-line p-8">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold tracking-tight">Dagboek</h3>
                <span class="rounded-full bg-accent-soft px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-accent">Plus</span>
            </div>
            <p class="mt-2 text-sm leading-relaxed text-muted">Stemming, energie en klachten, zie patronen over de week.</p>
        </div>
        <div class="rounded-3xl border border-line p-8">
            <div class="flex items-center gap-2">
                <h3 class="font-semibold tracking-tight">Wat nu?</h3>
                <span class="rounded-full bg-accent-soft px-2 py-0.5 text-[10px] font-medium uppercase tracking-wide text-accent">Plus</span>
            </div>
            <p class="mt-2 text-sm leading-relaxed text-muted">Kaarten bij spierpijn, hoofdpijn, moeheid en cravings.</p>
        </div>
    </div>
</section>

{{-- FINAL CTA --}}
<section class="border-t border-line bg-ink text-paper">
    <div class="mx-auto max-w-6xl px-4 py-16 text-center sm:px-6 sm:py-20">
        <p class="text-xs font-medium uppercase tracking-[0.2em] text-paper/45">Klaar om te beginnen?</p>
        <h2 class="mt-5 text-3xl font-semibold tracking-tight sm:text-4xl">Drie weken. Helder eten. Beter voelen.</h2>
        <p class="mx-auto mt-5 max-w-lg text-paper/70">
            Maak een account en open de kennisbank. U volgt de kuur van praktijk ARDRA, met Candidakuur altijd bij de hand.
        </p>
        <div class="mt-9 flex flex-wrap justify-center gap-4">
            @auth
                <a href="{{ route('kennisbank') }}" class="rounded-full bg-paper px-8 py-4 text-sm font-semibold text-ink transition hover:opacity-90">Open de kennisbank</a>
                @unless (auth()->user()->isPlus())
                    <a href="{{ route('plus.index') }}" class="rounded-full border border-paper/25 px-8 py-4 text-sm font-semibold text-paper transition hover:border-paper/50">Bekijk Plus</a>
                @endunless
            @else
                <a href="{{ route('register') }}" class="rounded-full bg-paper px-8 py-4 text-sm font-semibold text-ink transition hover:opacity-90">Account aanmaken</a>
                <a href="{{ route('login') }}" class="rounded-full border border-paper/25 px-8 py-4 text-sm font-semibold text-paper transition hover:border-paper/50">Ik heb al een account</a>
            @endauth
        </div>
        <p class="mt-8 text-xs text-paper/40">Geen medisch advies, begeleiding bij de kuur van praktijk ARDRA. Bij klachten: overleg met uw behandelaar.</p>
    </div>
</section>
@endsection

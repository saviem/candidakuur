@php
    $donateAmounts = config('services.mollie.donate_amounts', [5, 10, 25]);
    $mollieConfigured = filled(config('services.mollie.key'));
@endphp
<footer class="mt-auto border-t border-line bg-card">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-4">
        <div>
            <p class="text-sm font-semibold tracking-tight">Candidakuur</p>
            <p class="mt-1 text-xs uppercase tracking-[0.16em] text-muted">candidakuur.nl · praktijk ARDRA</p>
            <p class="mt-4 max-w-sm text-sm leading-relaxed text-muted">
                Doorzoekbare kennisbank bij de candidakuur van HP. H.A. Stormer. Praktijk voor Biologische Geneeswijzen, Haarlem.
            </p>
            <p class="mt-4 text-sm text-muted">
                <a class="hover:text-accent" href="mailto:hallo@candidakuur.nl">hallo@candidakuur.nl</a>
            </p>
        </div>
        <div class="text-sm text-muted">
            <p class="font-medium text-ink">Praktijk</p>
            <p class="mt-3 leading-relaxed">
                Houtmanpad 8 B 3<br>
                2015 EW Haarlem<br>
                <a class="hover:text-accent" href="tel:+31235441122">023 544 1122</a><br>
                <a class="hover:text-accent" href="mailto:pvbg@ardra.nl">pvbg@ardra.nl</a><br>
                <a class="hover:text-accent" href="https://www.ardra.nl" rel="noreferrer" target="_blank">arddra.nl</a>
            </p>
        </div>
        <div class="text-sm leading-relaxed text-muted">
            <p class="font-medium text-ink">Steun</p>
            <p class="mt-3">
                Doneer een eenmalige bijdrage. Dit houdt de kennisbank beschikbaar en ontgrendelt Plus niet.
            </p>
            @auth
                @if ($mollieConfigured)
                    <div class="mt-4 flex flex-wrap gap-2">
                        @foreach ($donateAmounts as $amount)
                            <form method="POST" action="{{ route('donate.checkout') }}">
                                @csrf
                                <input type="hidden" name="amount" value="{{ $amount }}">
                                <button type="submit" class="rounded-full border border-line bg-paper px-3.5 py-1.5 text-sm font-medium text-ink transition hover:border-ink/20">
                                    € {{ $amount }}
                                </button>
                            </form>
                        @endforeach
                    </div>
                @else
                    <p class="mt-4 text-sm text-limited">Doneren is tijdelijk niet beschikbaar.</p>
                @endif
            @else
                <p class="mt-4">
                    <a href="{{ route('login') }}" class="text-accent hover:underline">Log in om te doneren</a>
                </p>
            @endauth
        </div>
        <div class="text-sm leading-relaxed text-muted">
            <p class="font-medium text-ink">Let op</p>
            <p class="mt-3">
                Dit is voedingsadvies van de praktijk, geen vervanging van een arts. Nystatine is een geneesmiddel en alleen op voorschrift van je huisarts. Bij diabetes: overleg voordat je insuline aanpast.
            </p>
            <p class="mt-3">
                @auth
                    <a href="{{ route('guides.show', 'nystatine') }}" class="text-accent hover:underline">Meer over nystatine</a>
                    ·
                    <a href="{{ route('guides.show', 'contact') }}" class="text-accent hover:underline">Contact</a>
                @else
                    <a href="{{ route('login') }}" class="text-accent hover:underline">Inloggen voor de gids</a>
                    ·
                    <a href="mailto:pvbg@ardra.nl" class="text-accent hover:underline">Contact</a>
                @endauth
            </p>
        </div>
    </div>
    <div class="border-t border-line">
        <p class="mx-auto max-w-6xl px-4 py-4 text-xs text-muted sm:px-6">
            Candidakuur.nl is onderdeel van PureOrange BV
        </p>
    </div>
</footer>

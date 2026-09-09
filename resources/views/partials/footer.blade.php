<footer class="mt-auto border-t border-line bg-card">
    <div class="mx-auto grid max-w-6xl gap-10 px-4 py-12 sm:px-6 md:grid-cols-3">
        <div>
            <p class="text-sm font-semibold tracking-tight">Candidakuur</p>
            <p class="mt-1 text-xs uppercase tracking-[0.16em] text-muted">candidakuur.nl · praktijk ARDRA</p>
            <p class="mt-4 max-w-sm text-sm leading-relaxed text-muted">
                Doorzoekbare kennisbank bij de candidakuur van HP. H.A. Stormer. Praktijk voor Biologische Geneeswijzen, Haarlem.
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
</footer>

@php
    $compact = $compact ?? false;
    $initial = $initial ?? '';
@endphp

<div
    data-search-panel
    data-compact="{{ $compact ? '1' : '0' }}"
    data-suggestions-url="{{ route('search.suggestions') }}"
    data-results-url="{{ route('search.results') }}"
    data-unmatched-url="{{ route('search.unmatched') }}"
    data-search-url="{{ route('search') }}"
>
    <div class="relative">
        <label class="block">
            <span class="sr-only">Zoek een product</span>
            <input
                type="search"
                value="{{ $initial }}"
                autocomplete="off"
                placeholder="Mag ik tomaat? Brood, olijfolie, zalm…"
                class="js-search-input w-full rounded-[20px] border border-line bg-card px-5 py-4 text-base text-ink outline-none ring-accent/30 placeholder:text-muted focus:border-accent focus:ring-4"
            >
        </label>
        <ul class="js-suggest hidden absolute z-30 mt-2 w-full overflow-hidden rounded-[20px] border border-line bg-card py-1"></ul>
    </div>

    @php
        $popularTerms = $compact
            ? ['Tomaat', 'Brood', 'Melk', 'Koffie', 'Olijfolie', 'Suiker']
            : ['Tomaat', 'Brood', 'Melk', 'Koffie', 'Olijfolie', 'Suiker', 'Kaas', 'Kip'];
    @endphp
    <p class="js-popular mt-3 flex flex-wrap items-baseline gap-x-3 gap-y-1 text-sm">
        <span class="text-muted">Bijvoorbeeld</span>
        @foreach ($popularTerms as $term)
            <a href="{{ route('search', ['q' => $term]) }}" data-term="{{ $term }}" class="js-popular-term font-medium text-accent hover:underline">{{ $term }}</a>
        @endforeach
    </p>

    <div class="mt-4 flex flex-wrap gap-2">
        @foreach (['alles' => 'Alles', 'toegestaan' => 'Wel', 'niet_toegestaan' => 'Niet', 'beperkt' => 'Beperkt', 'voorwaardelijk' => 'Voorwaardelijk'] as $id => $label)
            <button type="button" data-status="{{ $id }}" class="js-filter cursor-pointer rounded-full px-3.5 py-1.5 text-sm font-medium {{ $id === 'alles' ? 'bg-ink text-paper' : 'bg-card text-muted ring-1 ring-line hover:text-ink' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <p class="js-count mt-6 text-sm text-muted"></p>
    <div class="js-empty hidden mt-4 rounded-[20px] border border-line bg-card p-5"></div>
    <div class="js-results mt-4 grid gap-3 {{ $compact ? '' : 'sm:grid-cols-2' }}"></div>
</div>

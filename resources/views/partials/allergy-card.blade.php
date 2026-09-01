@php $counts = $allergy->counts(); @endphp
<a href="{{ route('allergies.show', $allergy->slug) }}" class="flex flex-col justify-between rounded-[20px] border border-line bg-card p-5 transition hover:border-ink/20">
    <div>
        <p class="text-xs font-medium uppercase tracking-[0.14em] text-muted">{{ $allergy->short }}</p>
        <h3 class="mt-2 text-lg font-semibold tracking-tight text-ink">{{ $allergy->name }}</h3>
    </div>
    <div class="mt-6 flex gap-4 text-sm text-muted">
        <span><span class="font-semibold text-accent">{{ $counts['toegestaan'] }}</span> wel</span>
        <span><span class="font-semibold text-deny">{{ $counts['niet'] }}</span> niet</span>
        <span class="ml-auto tabular-nums">{{ $counts['total'] }}</span>
    </div>
</a>

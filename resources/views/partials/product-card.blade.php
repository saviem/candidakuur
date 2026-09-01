<a href="{{ route('products.show', $product->slug) }}" class="group flex flex-col rounded-[20px] border border-line bg-card p-4 transition hover:border-ink/20">
    <div class="mb-3 flex items-start justify-between gap-3">
        <h3 class="text-base font-semibold leading-snug text-ink group-hover:text-accent">{{ $product->name }}</h3>
        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide {{ $product->status->badgeClass() }}">
            {{ $product->status->label() }}
        </span>
    </div>
    <p class="line-clamp-3 text-sm leading-relaxed text-muted">{{ $product->why }}</p>
</a>

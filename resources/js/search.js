function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    })[char]);
}

function badge(label, cls) {
    return `<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide ${cls}">${label}</span>`;
}

function productCard(item) {
    return `<a href="${item.href}" class="group flex flex-col rounded-[20px] border border-line bg-card p-4 transition hover:border-ink/20">
        <div class="mb-3 flex items-start justify-between gap-3">
            <h3 class="text-base font-semibold leading-snug text-ink group-hover:text-accent">${escapeHtml(item.name)}</h3>
            ${badge(escapeHtml(item.status_label), item.status_class)}
        </div>
        <p class="line-clamp-3 text-sm leading-relaxed text-muted">${escapeHtml(item.why)}</p>
    </a>`;
}

function initSearchPanel(root) {
    const input = root.querySelector('.js-search-input');
    const suggestEl = root.querySelector('.js-suggest');
    const resultsEl = root.querySelector('.js-results');
    const countEl = root.querySelector('.js-count');
    const emptyEl = root.querySelector('.js-empty');
    const popularEl = root.querySelector('.js-popular');
    const compact = root.dataset.compact === '1';
    let status = 'alles';
    let active = 0;
    let suggestions = [];
    let unmatchedTimer;

    async function loadSuggestions(q) {
        if (q.length < 1) {
            suggestEl.classList.add('hidden');
            suggestions = [];
            return;
        }
        const url = new URL(root.dataset.suggestionsUrl, window.location.origin);
        url.searchParams.set('q', q);
        const res = await fetch(url);
        const data = await res.json();
        suggestions = data.suggestions ?? [];
        active = 0;
        renderSuggestions();
    }

    function renderSuggestions() {
        if (!suggestions.length) {
            suggestEl.classList.add('hidden');
            suggestEl.innerHTML = '';
            return;
        }
        suggestEl.innerHTML = suggestions
            .map((item, index) => {
                const extra = item.status_label
                    ? badge(item.status_label, item.status_class)
                    : `<span class="text-xs text-muted">${escapeHtml(item.hint ?? '')}</span>`;
                const hint = item.hint && item.status_label
                    ? `<span class="ml-2 text-xs font-normal text-muted">${escapeHtml(item.hint)}</span>`
                    : '';
                return `<li>
                    <a href="${escapeHtml(item.href)}" class="group flex w-full items-center justify-between gap-3 px-4 py-2.5 text-sm ${index === active ? 'bg-paper' : 'hover:bg-paper'}">
                        <span class="font-medium text-accent group-hover:underline">${escapeHtml(item.title)}${hint}</span>
                        ${extra}
                    </a>
                </li>`;
            })
            .join('');
        suggestEl.classList.remove('hidden');
        suggestEl.querySelectorAll('a').forEach((link) => {
            link.addEventListener('mousedown', (e) => e.preventDefault());
            link.addEventListener('click', (e) => {
                if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) {
                    return;
                }
                e.preventDefault();
                window.location.href = link.href;
            });
        });
    }

    async function loadResults() {
        const q = input.value.trim();
        if (compact && q.length === 0 && status === 'alles') {
            resultsEl.innerHTML = '';
            countEl.textContent = '';
            emptyEl.classList.add('hidden');
            return;
        }
        const url = new URL(root.dataset.resultsUrl, window.location.origin);
        url.searchParams.set('q', q);
        url.searchParams.set('status', status);
        const res = await fetch(url);
        const data = await res.json();
        const items = data.products ?? [];
        const limit = compact ? 8 : 120;
        countEl.textContent = q
            ? `${data.count} ${data.count === 1 ? 'resultaat' : 'resultaten'}`
            : `${data.count} producten`;
        if (compact && q && data.count > 8) {
            const more = new URL(root.dataset.searchUrl, window.location.origin);
            more.searchParams.set('q', q);
            countEl.innerHTML += ` · <a href="${more}" class="font-medium text-accent hover:underline">Alle resultaten</a>`;
        }
        resultsEl.innerHTML = items.slice(0, limit).map(productCard).join('');
        if (q.length >= 3 && items.length === 0 && suggestions.length === 0) {
            emptyEl.classList.remove('hidden');
            emptyEl.innerHTML = `<p class="font-medium text-ink">Niets gevonden voor “${q}”</p>
                <p class="mt-1 text-sm text-muted">We slaan deze zoekopdracht op, zodat de kennisbank in de admin aangevuld kan worden.</p>`;
        } else {
            emptyEl.classList.add('hidden');
        }
        scheduleUnmatched(q, items.length === 0 && suggestions.length === 0);
        if (popularEl) {
            popularEl.classList.toggle('hidden', q.length > 0);
        }
    }

    function scheduleUnmatched(q, miss) {
        clearTimeout(unmatchedTimer);
        if (!miss || q.length < 3) return;
        unmatchedTimer = setTimeout(() => {
            fetch(root.dataset.unmatchedUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                },
                body: JSON.stringify({ query: q }),
            }).catch(() => {});
        }, 1100);
    }

    input.addEventListener('input', () => {
        loadSuggestions(input.value.trim());
        loadResults();
    });
    input.addEventListener('focus', () => {
        if (input.value.trim()) loadSuggestions(input.value.trim());
    });
    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowDown' && suggestions.length) {
            e.preventDefault();
            active = (active + 1) % suggestions.length;
            renderSuggestions();
        } else if (e.key === 'ArrowUp' && suggestions.length) {
            e.preventDefault();
            active = (active - 1 + suggestions.length) % suggestions.length;
            renderSuggestions();
        } else if (e.key === 'Enter' && suggestions[active]) {
            e.preventDefault();
            window.location.href = suggestions[active].href;
        } else if (e.key === 'Escape') {
            suggestEl.classList.add('hidden');
        }
    });
    document.addEventListener('mousedown', (e) => {
        if (!root.contains(e.target)) suggestEl.classList.add('hidden');
    });
    root.querySelectorAll('.js-filter').forEach((button) => {
        button.addEventListener('click', () => {
            status = button.dataset.status;
            root.querySelectorAll('.js-filter').forEach((b) => {
                b.className =
                    b === button
                        ? 'js-filter cursor-pointer rounded-full px-3.5 py-1.5 text-sm font-medium bg-ink text-paper'
                        : 'js-filter cursor-pointer rounded-full px-3.5 py-1.5 text-sm font-medium bg-card text-muted ring-1 ring-line hover:text-ink';
            });
            loadResults();
        });
    });
    root.querySelectorAll('.js-popular-term').forEach((link) => {
        link.addEventListener('click', (e) => {
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) {
                return;
            }
            e.preventDefault();
            input.value = link.dataset.term;
            input.dispatchEvent(new Event('input'));
        });
    });

    if (input.value.trim() || !compact) {
        loadResults();
        if (input.value.trim()) loadSuggestions(input.value.trim());
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-search-panel]').forEach(initSearchPanel);
    document.addEventListener('keydown', (e) => {
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            const search = document.querySelector('.js-search-input');
            if (search) search.focus();
            else window.location.href = '/zoeken';
        }
    });
});

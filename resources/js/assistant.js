function escapePickerHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
    })[char]);
}

function initProductPicker(root) {
    const input = root.querySelector('.js-picker-input');
    const slug = root.querySelector('.js-picker-slug');
    const suggestEl = root.querySelector('.js-picker-suggest');
    let suggestions = [];
    let active = 0;
    let typedValue = input.value;

    async function loadSuggestions(q) {
        if (q.length < 1) {
            suggestions = [];
            suggestEl.classList.add('hidden');
            suggestEl.innerHTML = '';
            return;
        }

        const url = new URL(root.dataset.suggestionsUrl, window.location.origin);
        url.searchParams.set('q', q);
        url.searchParams.set('kind', 'product');
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
                const badge = item.status_label
                    ? `<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide ${item.status_class}">${escapePickerHtml(item.status_label)}</span>`
                    : '';
                return `<li>
                    <button type="button" data-index="${index}" class="flex w-full items-center justify-between gap-3 px-4 py-2.5 text-left text-sm ${index === active ? 'bg-paper' : 'hover:bg-paper'}">
                        <span class="font-medium text-ink">${escapePickerHtml(item.title)}</span>
                        ${badge}
                    </button>
                </li>`;
            })
            .join('');
        suggestEl.classList.remove('hidden');
        suggestEl.querySelectorAll('button').forEach((button) => {
            button.addEventListener('mousedown', (event) => event.preventDefault());
            button.addEventListener('click', () => choose(Number(button.dataset.index)));
        });
    }

    function choose(index) {
        const item = suggestions[index];
        if (!item) {
            return;
        }

        input.value = item.title;
        slug.value = item.slug ?? '';
        typedValue = item.title;
        suggestions = [];
        suggestEl.classList.add('hidden');
        suggestEl.innerHTML = '';
    }

    input.addEventListener('input', () => {
        if (input.value !== typedValue) {
            slug.value = '';
        }
        typedValue = input.value;
        loadSuggestions(input.value.trim());
    });
    input.addEventListener('focus', () => {
        if (input.value.trim()) {
            loadSuggestions(input.value.trim());
        }
    });
    input.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowDown' && suggestions.length) {
            event.preventDefault();
            active = (active + 1) % suggestions.length;
            renderSuggestions();
        } else if (event.key === 'ArrowUp' && suggestions.length) {
            event.preventDefault();
            active = (active - 1 + suggestions.length) % suggestions.length;
            renderSuggestions();
        } else if (event.key === 'Enter' && suggestions[active]) {
            event.preventDefault();
            choose(active);
        } else if (event.key === 'Escape') {
            suggestEl.classList.add('hidden');
        }
    });
    document.addEventListener('mousedown', (event) => {
        if (!root.contains(event.target)) {
            suggestEl.classList.add('hidden');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-product-picker]').forEach(initProductPicker);

    const form = document.querySelector('[data-assistant-form]');
    const submit = form?.querySelector('.js-assistant-submit');
    form?.addEventListener('submit', () => {
        if (!submit) {
            return;
        }
        submit.disabled = true;
        submit.textContent = 'Menu maken…';
    });
});

function normalizeEntries(paramsOrEntries) {
    if (!paramsOrEntries) return [];
    if (Array.isArray(paramsOrEntries)) return paramsOrEntries.filter(Boolean);
    return Object.entries(paramsOrEntries);
}

function mergeEntries(baseEntries, overrideEntries) {
    const map = new Map();
    normalizeEntries(baseEntries).forEach(([k, v]) => map.set(k, v));
    normalizeEntries(overrideEntries).forEach(([k, v]) => map.set(k, v));
    return Array.from(map.entries());
}

function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta) return meta.getAttribute('content');
    const xsrf = getCookie('XSRF-TOKEN');
    if (xsrf) {
        try { return decodeURIComponent(xsrf); } catch { return xsrf; }
    }
    return null;
}

function buildUrl(baseUrl, paramsOrEntries) {
    const url = new URL(baseUrl, window.location.origin);
    // merge with existing (current location)
    const current = new URLSearchParams(window.location.search);
    current.forEach((v, k) => url.searchParams.append(k, v));

    const entries = normalizeEntries(paramsOrEntries);
    const keysToReplace = new Set(entries.map(([k]) => k));
    keysToReplace.forEach((k) => url.searchParams.delete(k));
    entries.forEach(([k, v]) => {
        if (v !== undefined && v !== null && v !== '') {
            url.searchParams.append(k, v);
        }
    });
    return url.toString();
}

async function loadAsyncList(container, paramsOrEntries = {}) {
    const src = container.getAttribute('data-src');
    if (!src) return;
    const entries = normalizeEntries(paramsOrEntries);
    // Always include current view and partial=1
    const view = container.getAttribute('data-view') || new URLSearchParams(window.location.search).get('view') || 'list';
    entries.push(['view', view]);
    entries.push(['partial', '1']);
    const method = (container.getAttribute('data-method') || 'get').toLowerCase();
    // Remember the last used params for this container (for pagination/toggles)
    container._asyncParams = entries;

    container.setAttribute('aria-busy', 'true');
    container.innerHTML = `
        <div class="py-10 flex items-center justify-center">
            <svg class="animate-spin h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
        </div>`;

    try {
        let res;
        if (method === 'post') {
            const body = new URLSearchParams(entries);
            const csrf = getCsrfToken();
            res = await fetch(src, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded',
                    ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
                },
                credentials: 'same-origin',
                body,
            });
        } else {
            const url = buildUrl(src, entries);
            res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' });
        }
        const html = await res.text();
        container.innerHTML = html;
        container.setAttribute('aria-busy', 'false');
    } catch (e) {
        console.error(e);
        container.innerHTML = `<div class="p-6 text-sm text-red-600">Failed to load. <button class="underline" data-async-retry>Retry</button></div>`;
    }
}

// Keep history/URL untouched for simplicity

export function initAsyncLists() {
    const containers = Array.from(document.querySelectorAll('[data-async-list]'));
    if (containers.length === 0) return;

    containers.forEach(container => loadAsyncList(container));

    // Toggle icon helper (list vs cards)
    const setToggleIcon = (link, view) => {
        const target = link.querySelector('[data-icon="toggle"]') || link;
        const grid = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h7v7H4V6zm9 0h7v7h-7V6zM4 15h7v3H4v-3zm9 0h7v3h-7v-3z"/></svg>';
        const list = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>';
        target.innerHTML = (view === 'list') ? grid : list;
    };

    // Initialize toggle icons based on current containers' view
    document.querySelectorAll('[data-async-toggle-view]').forEach((link) => {
        const targetSel = link.getAttribute('data-async-target');
        const container = targetSel ? document.querySelector(targetSel) : document.querySelector('[data-async-list]');
        const currentView = container?.getAttribute('data-view') || 'list';
        setToggleIcon(link, currentView);
    });

    // Serialize a form supporting multi-selects and checkbox arrays
    const serializeForm = (form) => {
        const entries = [];
        const elements = Array.from(form.elements);
        elements.forEach((el) => {
            if (!el.name || el.disabled) return;
            const name = el.name;
            if (el.tagName === 'SELECT') {
                const select = el;
                const isMultiple = select.multiple;
                const selected = Array.from(select.selectedOptions).map(o => o.value);
                if (isMultiple) {
                    selected.forEach(v => entries.push([name, v]));
                } else if (selected[0] !== undefined) {
                    entries.push([name, selected[0]]);
                }
                return;
            }
            if (el.type === 'checkbox') {
                if (el.checked) entries.push([name, el.value || '1']);
                return;
            }
            if (el.type === 'radio') {
                if (el.checked) entries.push([name, el.value]);
                return;
            }
            entries.push([name, el.value]);
        });
        return entries;
    };

    // Intercept search form submits
    document.body.addEventListener('submit', (e) => {
        const form = e.target.closest('form[data-async-target]');
        if (!form) return;
        e.preventDefault();
        const targetSel = form.getAttribute('data-async-target');
        const container = document.querySelector(targetSel);
        if (!container) return;
        const method = (container.getAttribute('data-method') || 'get').toLowerCase();
        const entries = serializeForm(form);
        entries.push(['page', '']); // reset pagination on new search/filter
        loadAsyncList(container, entries);
    });

    // Reactive search (debounced input) for forms with data-async-target
    document.body.addEventListener('input', (e) => {
        const input = e.target.closest('form[data-async-target] input[name]');
        if (!input) return;
        const form = input.closest('form[data-async-target]');
        const targetSel = form.getAttribute('data-async-target');
        const container = document.querySelector(targetSel);
        if (!container) return;
        if (form._asyncTimer) clearTimeout(form._asyncTimer);
        form._asyncTimer = setTimeout(() => {
            const entries = serializeForm(form);
            entries.push(['page', '']);
            loadAsyncList(container, entries);
        }, 350);
    });

    // Intercept view toggle & pagination links (generic across pages)
    document.body.addEventListener('click', (e) => {
        const toggle = e.target.closest('[data-async-toggle-view]');
        if (toggle) {
            e.preventDefault();
            const targetSel = toggle.getAttribute('data-async-target');
            const container = targetSel
                ? document.querySelector(targetSel)
                : (toggle.closest('[data-async-list]') || document.querySelector('[data-async-list]'));
            if (!container) return;
            const currentView = container.getAttribute('data-view') || 'list';
            const nextView = currentView === 'list' ? 'cards' : 'list';
            container.setAttribute('data-view', nextView);
            setToggleIcon(toggle, nextView);
            loadAsyncList(container, mergeEntries(container._asyncParams || [], [['view', nextView]]));
            return;
        }

        const link = e.target.closest('[data-async-link], .async-pagination a');
        if (!link) return;
        let container = null;
        if (link.matches('.async-pagination a')) {
            container = link.closest('[data-async-list]');
        }
        if (!container) {
            const targetSel = link.getAttribute('data-async-target');
            container = targetSel ? document.querySelector(targetSel) : document.querySelector('[data-async-list]');
        }
        if (!container) return;
        e.preventDefault();
        let params = [];
        if (link.hasAttribute('data-async-link')) {
            const url = new URL(link.getAttribute('href'), window.location.origin);
            params = Array.from(url.searchParams.entries());
        } else if (link.closest('.async-pagination')) {
            const url = new URL(link.getAttribute('href'), window.location.origin);
            const page = url.searchParams.get('page');
            params = mergeEntries(container._asyncParams || [], [['page', page]]);
        }
        loadAsyncList(container, params);
    });

    // Intercept change events for selects/checkboxes/radios to update instantly
    document.body.addEventListener('change', (e) => {
        const form = e.target.closest('form[data-async-target]');
        if (!form) return;
        const targetSel = form.getAttribute('data-async-target');
        const container = document.querySelector(targetSel);
        if (!container) return;
        const entries = serializeForm(form);
        entries.push(['page', '']);
        loadAsyncList(container, entries);
    });

    // Support retry button
    document.body.addEventListener('click', (e) => {
        const retry = e.target.closest('[data-async-retry]');
        if (!retry) return;
        const container = retry.closest('[data-async-list]');
        if (container) loadAsyncList(container);
    });

    // No popstate handler for simplicity
}

// Auto-init
document.addEventListener('DOMContentLoaded', initAsyncLists);



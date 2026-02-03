/**
 * Admin panel common JS
 * - AJAX modal loader: [data-ajax-modal="true"]
 * - Delete confirm modal: [data-delete-modal="true"]
 * - Toasts: window.show_toaster(title, message, type)
 */

function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta?.getAttribute('content') || '';
}

function $(selector, root = document) {
    return root.querySelector(selector);
}

function show(el) {
    el?.classList.remove('hidden');
    if (el) el.setAttribute('aria-hidden', 'false');
}

function hide(el) {
    el?.classList.add('hidden');
    if (el) el.setAttribute('aria-hidden', 'true');
}

function sizeToMaxWidth(size) {
    switch ((size || 'md').toLowerCase()) {
        case 'sm':
            return 'max-w-lg';
        case 'lg':
            return 'max-w-4xl';
        case 'xl':
            return 'max-w-5xl';
        case 'full':
            return 'max-w-7xl';
        case 'md':
        default:
            return 'max-w-2xl';
    }
}

function replaceMaxWidthClass(containerEl, size) {
    if (!containerEl) return;
    const allowed = ['max-w-lg', 'max-w-2xl', 'max-w-4xl', 'max-w-5xl', 'max-w-7xl'];
    containerEl.classList.remove(...allowed);
    containerEl.classList.add(sizeToMaxWidth(size));
}

function toast(type, title, message) {
    const container = $('#toast-container');
    if (!container) return;

    const colors =
        type === 'success'
            ? { ring: 'ring-emerald-200', bg: 'bg-emerald-50', text: 'text-emerald-900', sub: 'text-emerald-800' }
            : type === 'warning'
              ? { ring: 'ring-amber-200', bg: 'bg-amber-50', text: 'text-amber-900', sub: 'text-amber-800' }
              : { ring: 'ring-red-200', bg: 'bg-red-50', text: 'text-red-900', sub: 'text-red-800' };

    const el = document.createElement('div');
    el.className = `rounded-2xl ${colors.bg} p-4 shadow-lg ring-1 ${colors.ring}`;
    el.innerHTML = `
        <div class="flex items-start justify-between gap-3">
            <div>
                <div class="text-sm font-semibold ${colors.text}">${title || 'Notice'}</div>
                <div class="mt-1 text-sm ${colors.sub}">${message || ''}</div>
            </div>
            <button type="button" class="rounded-xl bg-white/60 px-2 py-1 text-sm font-medium ${colors.text} hover:bg-white" data-toast-close="true">✕</button>
        </div>
    `;

    container.appendChild(el);

    const t = window.setTimeout(() => {
        el.remove();
    }, 4500);

    el.addEventListener('click', (e) => {
        const btn = e.target instanceof HTMLElement ? e.target.closest('[data-toast-close="true"]') : null;
        if (!btn) return;
        window.clearTimeout(t);
        el.remove();
    });
}

// Backward-compatible API name from your snippet.
window.show_toaster = function (title, message, type) {
    toast(type || 'error', title, message);
};

function safeCallGlobal(name) {
    const fn = window[name];
    if (typeof fn === 'function') fn();
}

function openCommonModal({ title, size, url }) {
    const modal = $('#common-modal');
    if (!modal) return;

    const titleEl = $('.modal-title', modal);
    const bodyEl = $('.modal-body', modal);
    const containerEl = modal.querySelector('.relative.mx-auto');

    if (titleEl) titleEl.textContent = title || 'Modal';
    if (bodyEl) bodyEl.innerHTML = '<div class="text-sm text-slate-600">Loading…</div>';
    replaceMaxWidthClass(containerEl, size);

    show(modal);
    document.documentElement.classList.add('overflow-hidden');

    fetch(url, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        cache: 'no-store',
        credentials: 'same-origin',
    })
        .then(async (res) => {
            if (res.status === 401) {
                location.reload();
                return;
            }
            if (!res.ok) {
                const text = await res.text();
                throw new Error(text || 'Request failed');
            }
            return res.text();
        })
        .then((html) => {
            if (!bodyEl) return;
            bodyEl.innerHTML = html || '';

            // Question form: cascade subject -> topics (scripts in innerHTML don't run)
            const subjectSel = bodyEl.querySelector('#question_subject_id');
            const topicSel = bodyEl.querySelector('#question_topic_id');
            if (subjectSel && topicSel) {
                const url = subjectSel.getAttribute('data-topics-url');
                if (url) {
                    subjectSel.addEventListener('change', function () {
                        const subjectId = this.value;
                        topicSel.innerHTML = '<option value="">— None —</option>';
                        topicSel.value = '';
                        if (!subjectId) return;
                        const fullUrl = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'subject_id=' + encodeURIComponent(subjectId);
                        fetch(fullUrl, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
                            credentials: 'same-origin',
                        })
                            .then((r) => r.json())
                            .then((topics) => {
                                topics.forEach((t) => {
                                    const opt = document.createElement('option');
                                    opt.value = t.id;
                                    opt.textContent = t.name;
                                    topicSel.appendChild(opt);
                                });
                            });
                    });
                }
            }

            // Optional init hooks (like your snippet).
            safeCallGlobal('dataTable');
            safeCallGlobal('select2');
            safeCallGlobal('summernote');
        })
        .catch((err) => {
            window.show_toaster('Error', err?.message || 'Something went wrong', 'error');
        });
}

function closeCommonModal() {
    hide($('#common-modal'));
    document.documentElement.classList.remove('overflow-hidden');
}

function openDeleteModal(actionUrl) {
    const modal = $('#delete-modal');
    const form = $('#deleteItem', modal);
    if (form && actionUrl) form.setAttribute('action', actionUrl);
    show(modal);
    document.documentElement.classList.add('overflow-hidden');
}

function closeDeleteModal() {
    hide($('#delete-modal'));
    document.documentElement.classList.remove('overflow-hidden');
}

document.addEventListener('click', (e) => {
    const target = e.target instanceof HTMLElement ? e.target : null;
    if (!target) return;

    const ajaxTrigger = target.closest('[data-ajax-modal="true"]');
    if (ajaxTrigger) {
        e.preventDefault();
        openCommonModal({
            title: ajaxTrigger.getAttribute('data-title') || '',
            size: ajaxTrigger.getAttribute('data-size') || 'md',
            url: ajaxTrigger.getAttribute('data-url') || '',
        });
        return;
    }

    const deleteTrigger = target.closest('[data-delete-modal="true"]');
    if (deleteTrigger) {
        e.preventDefault();
        openDeleteModal(deleteTrigger.getAttribute('data-url') || '');
        return;
    }

    if (target.closest('[data-modal-close="true"]')) {
        e.preventDefault();
        closeCommonModal();
        return;
    }

    if (target.closest('[data-delete-close="true"]')) {
        e.preventDefault();
        closeDeleteModal();
        return;
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    closeCommonModal();
    closeDeleteModal();
});

// Questions index: cascade subject -> topic in filter form
function wireQuestionFilterCascade() {
    const subjectSel = document.getElementById('filter_subject_id');
    const topicSel = document.getElementById('filter_topic_id');
    const url = subjectSel?.getAttribute('data-topics-url');
    if (!subjectSel || !topicSel || !url) return;
    subjectSel.addEventListener('change', function () {
        const subjectId = this.value;
        topicSel.innerHTML = '<option value="">All</option>';
        topicSel.value = '';
        if (!subjectId) return;
        const fullUrl = url + (url.indexOf('?') >= 0 ? '&' : '?') + 'subject_id=' + encodeURIComponent(subjectId);
        fetch(fullUrl, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
            credentials: 'same-origin',
        })
            .then((r) => r.json())
            .then((topics) => {
                topics.forEach((t) => {
                    const opt = document.createElement('option');
                    opt.value = t.id;
                    opt.textContent = t.name;
                    topicSel.appendChild(opt);
                });
            });
    });
}

// Push notifications (FCM) - reuse same click wiring
import { wirePushEnableButtons } from './fcm_client.js';
document.addEventListener('DOMContentLoaded', () => {
    wirePushEnableButtons();
    wireQuestionFilterCascade();
});


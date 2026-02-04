function $(selector, root = document) {
    return root.querySelector(selector);
}

function openStudentSidebar() {
    const drawer = $('#student-game-sidebar');
    const backdrop = $('#student-game-sidebar-backdrop');
    if (!drawer || !backdrop) return;
    drawer.classList.remove('-translate-x-full');
    backdrop.classList.remove('hidden');
    document.documentElement.classList.add('overflow-hidden');
}

function closeStudentSidebar() {
    const drawer = $('#student-game-sidebar');
    const backdrop = $('#student-game-sidebar-backdrop');
    if (!drawer || !backdrop) return;
    drawer.classList.add('-translate-x-full');
    backdrop.classList.add('hidden');
    document.documentElement.classList.remove('overflow-hidden');
}

function setAuthModalNext(nextUrl) {
    const modal = document.querySelector('[data-auth-modal="true"]');
    if (!modal) return;

    const googleLink = modal.querySelector('[data-auth-google-link="true"]');
    if (googleLink && nextUrl) {
        try {
            const u = new URL(googleLink.getAttribute('href') || '', window.location.origin);
            u.searchParams.set('next', nextUrl);
            googleLink.setAttribute('href', u.toString());
        } catch {
            // ignore
        }
    }
}

function openAuthModal(nextUrl) {
    const modal = document.querySelector('[data-auth-modal="true"]');
    if (!modal) return;
    setAuthModalNext(nextUrl);
    modal.classList.remove('hidden');
    document.documentElement.classList.add('overflow-hidden');
}

function closeAuthModal() {
    const modal = document.querySelector('[data-auth-modal="true"]');
    if (!modal) return;
    modal.classList.add('hidden');
    document.documentElement.classList.remove('overflow-hidden');
}

document.addEventListener('click', (e) => {
    const target = e.target instanceof Element ? e.target : null;
    if (!target) return;

    // Copy-to-clipboard
    const copyBtn = target.closest('[data-copy-text]');
    if (copyBtn) {
        e.preventDefault();
        const text = copyBtn.getAttribute('data-copy-text') || '';
        if (!text) return;

        const done = () => {
            const old = copyBtn.textContent || 'Copy';
            copyBtn.textContent = 'Copied';
            window.setTimeout(() => {
                copyBtn.textContent = old;
            }, 1200);
        };

        const fail = () => {
            const old = copyBtn.textContent || 'Copy';
            copyBtn.textContent = 'Failed';
            window.setTimeout(() => {
                copyBtn.textContent = old;
            }, 1200);
        };

        if (navigator.clipboard?.writeText) {
            navigator.clipboard.writeText(text).then(done).catch(fail);
            return;
        }

        try {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.setAttribute('readonly', 'true');
            ta.style.position = 'fixed';
            ta.style.left = '-9999px';
            document.body.appendChild(ta);
            ta.select();
            const ok = document.execCommand('copy');
            document.body.removeChild(ta);
            if (ok) done();
            else fail();
        } catch {
            fail();
        }
        return;
    }

    if (target.closest('[data-auth-modal-open="true"]')) {
        e.preventDefault();
        const opener = target.closest('[data-auth-modal-open="true"]');
        const next = opener?.getAttribute('data-auth-next') || opener?.getAttribute('href') || window.location.href;
        closeStudentSidebar();
        openAuthModal(next);
        return;
    }

    if (target.closest('[data-auth-modal-close="true"]')) {
        e.preventDefault();
        closeAuthModal();
        return;
    }

    if (target.closest('[data-student-sidebar-open="true"]')) {
        e.preventDefault();
        openStudentSidebar();
        return;
    }

    if (target.closest('[data-student-sidebar-close="true"]')) {
        e.preventDefault();
        closeStudentSidebar();
        return;
    }
});

document.addEventListener('keydown', (e) => {
    if (e.key !== 'Escape') return;
    closeAuthModal();
    closeStudentSidebar();
});

document.addEventListener('DOMContentLoaded', () => {
    const marker = document.querySelector('[data-auth-modal-autoshow]');
    const shouldOpen = marker?.getAttribute('data-auth-modal-autoshow') === '1';
    if (shouldOpen) openAuthModal(window.location.href);
});

// Bookmark toggle via AJAX (no page refresh)
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!form || form.tagName !== 'FORM') return;
    if (!form.classList.contains('bookmark-toggle-form') && !(form.getAttribute('action') || '').includes('bookmarks')) return;

    e.preventDefault();
    const btn = form.querySelector('button[type="submit"]');
    const removeOnUnbookmark = form.getAttribute('data-remove-on-unbookmark') === '1' || form.getAttribute('data-remove-on-unbookmark') === 'true';
    const card = removeOnUnbookmark ? form.closest('.revision-bookmark-card, .border-b') : null;
    const originalText = btn ? btn.textContent : '';

    if (btn) {
        btn.disabled = true;
        btn.textContent = '…';
    }

    fetch(form.action, {
        method: 'POST',
        body: new FormData(form),
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
    })
        .then((r) => r.json())
        .then((data) => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = data.label != null ? data.label : originalText;
            }
            if (removeOnUnbookmark && data.bookmarked === false && card) {
                card.style.transition = 'opacity 0.2s';
                card.style.opacity = '0';
                setTimeout(() => card.remove(), 220);
            }
        })
        .catch(() => {
            if (btn) {
                btn.disabled = false;
                btn.textContent = originalText;
            }
            form.submit();
        });
});

// Quiz countdown timer (optional)
function formatTime(seconds) {
    const s = Math.max(0, Math.floor(seconds));
    const m = Math.floor(s / 60);
    const r = s % 60;
    return `${m}:${String(r).padStart(2, '0')}`;
}

let quizTimerIntervalId = null;

function initQuizTimer() {
    if (quizTimerIntervalId !== null) {
        clearInterval(quizTimerIntervalId);
        quizTimerIntervalId = null;
    }

    const el = document.querySelector('[data-quiz-deadline-iso]');
    if (!el) return;

    const deadlineIso = el.getAttribute('data-quiz-deadline-iso');
    if (!deadlineIso) return;

    const deadlineMs = Date.parse(deadlineIso);
    if (!Number.isFinite(deadlineMs)) return;

    const tick = () => {
        const left = Math.ceil((deadlineMs - Date.now()) / 1000);
        el.textContent = formatTime(left);

        if (left <= 0) {
            const form = document.querySelector('[data-quiz-autosubmit="true"]');
            if (form && form instanceof HTMLFormElement) {
                form.submit();
            }
        }
    };

    tick();
    quizTimerIntervalId = window.setInterval(tick, 500);
}

window.initQuizTimer = initQuizTimer;
document.addEventListener('DOMContentLoaded', initQuizTimer);

// Generic countdown (e.g., contest start)
function initCountdowns() {
    const nodes = document.querySelectorAll('[data-countdown-to-iso]');
    if (!nodes.length) return;

    const tick = () => {
        const nowMs = Date.now();

        nodes.forEach((node) => {
            const iso = node.getAttribute('data-countdown-to-iso');
            if (!iso) return;
            const target = Date.parse(iso);
            if (!Number.isFinite(target)) return;

            const left = Math.ceil((target - nowMs) / 1000);
            node.textContent = formatTime(left);
        });
    };

    tick();
    window.setInterval(tick, 500);
}

document.addEventListener('DOMContentLoaded', initCountdowns);

// Interstitial ad (MVP scaffold): show only on result pages, every N results.
function initInterstitialAd() {
    const modal = document.querySelector('[data-ad-interstitial-modal="true"]');
    if (!modal) return;

    const enabled = modal.getAttribute('data-ad-enabled') === '1';
    const every = Number.parseInt(modal.getAttribute('data-ad-every') || '0', 10);
    if (!enabled || !Number.isFinite(every) || every <= 0) return;

    const key = 'ad_result_counter_v1';
    const raw = window.localStorage.getItem(key);
    const current = Number.parseInt(raw || '0', 10);
    const next = Number.isFinite(current) ? current + 1 : 1;
    window.localStorage.setItem(key, String(next));

    if (next % every !== 0) return;

    modal.classList.remove('hidden');
    document.documentElement.classList.add('overflow-hidden');
}

document.addEventListener('click', (e) => {
    const target = e.target instanceof Element ? e.target : null;
    if (!target) return;

    if (target.closest('[data-ad-close="true"]')) {
        e.preventDefault();
        const modal = document.querySelector('[data-ad-interstitial-modal="true"]');
        if (modal) modal.classList.add('hidden');
        document.documentElement.classList.remove('overflow-hidden');
        return;
    }
});

document.addEventListener('DOMContentLoaded', initInterstitialAd);

// Club member search (admin-only) - AJAX, no refresh
function debounce(fn, waitMs) {
    let t = null;
    return (...args) => {
        if (t) window.clearTimeout(t);
        t = window.setTimeout(() => fn(...args), waitMs);
    };
}

function escapeHtml(text) {
    return String(text)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function initClubMemberSearch() {
    const root = document.querySelector('[data-club-member-search="true"]');
    if (!root) return;

    const input = root.querySelector('[data-club-member-search-input="true"]');
    const clearBtn = root.querySelector('[data-club-member-search-clear="true"]');
    const status = root.querySelector('[data-club-member-search-status="true"]');
    const results = root.querySelector('[data-club-member-search-results="true"]');
    if (!(input instanceof HTMLInputElement) || !(results instanceof HTMLElement)) return;

    const searchEndpoint = root.getAttribute('data-search-endpoint') || '';
    const addEndpoint = root.getAttribute('data-add-endpoint') || '';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    let lastController = null;

    const setStatus = (msg) => {
        if (status) status.textContent = msg || '';
    };

    const setResultsHtml = (html) => {
        results.innerHTML = html;
        const has = html.trim().length > 0;
        results.classList.toggle('hidden', !has);
    };

    const renderItems = (items) => {
        if (!Array.isArray(items) || items.length === 0) {
            setResultsHtml(`<div class="px-4 py-3 text-sm text-slate-300">No users found.</div>`);
            return;
        }

        const rows = items
            .map((u) => {
                const name = escapeHtml(u?.name || '—');
                const email = escapeHtml(u?.email || '');
                const username = u?.username ? escapeHtml(u.username) : '';
                const blocked = u?.blocked ? `<div class="mt-1 text-xs font-semibold text-red-200">Blocked</div>` : '';
                const uId = Number.parseInt(String(u?.id || '0'), 10);

                return `
<div class="flex items-center justify-between gap-3 border-b border-white/10 px-4 py-3 last:border-b-0" data-member-row-id="${uId}">
  <div class="min-w-0">
    <div class="text-sm font-semibold text-white truncate">${name} <span class="ml-2 text-xs text-slate-400">#${uId}</span></div>
    <div class="mt-1 text-xs text-slate-300 truncate">${email}</div>
    ${username ? `<div class="mt-0.5 text-xs text-slate-400 truncate">@${username}</div>` : ''}
    ${blocked}
  </div>
  <button type="button"
          class="bg-emerald-500/80 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-500"
          data-add-user-id="${uId}">
    Add
  </button>
</div>`;
            })
            .join('');

        setResultsHtml(rows);
    };

    const doSearch = async () => {
        const q = input.value.trim();
        if (q.length < 2) {
            setStatus('Type at least 2 characters to search.');
            setResultsHtml('');
            return;
        }

        if (!searchEndpoint) return;

        setStatus('Searching…');

        if (lastController) lastController.abort();
        lastController = new AbortController();

        try {
            const u = new URL(searchEndpoint, window.location.origin);
            u.searchParams.set('q', q);

            const res = await fetch(u.toString(), {
                headers: { Accept: 'application/json' },
                signal: lastController.signal,
            });
            if (!res.ok) throw new Error('bad_status');
            const data = await res.json();
            renderItems(data?.items || []);
            setStatus('');
        } catch (e) {
            if (e?.name === 'AbortError') return;
            setStatus('Search failed. Try again.');
        }
    };

    const debounced = debounce(doSearch, 250);
    input.addEventListener('input', debounced);

    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            input.value = '';
            setStatus('');
            setResultsHtml('');
            input.focus();
        });
    }

    // AJAX add member
    results.addEventListener('click', async (e) => {
        const target = e.target instanceof Element ? e.target : null;
        if (!target) return;
        const btn = target.closest('[data-add-user-id]');
        if (!btn) return;

        const userId = btn.getAttribute('data-add-user-id') || '';
        const idNum = Number.parseInt(userId, 10);
        if (!Number.isFinite(idNum) || idNum <= 0) return;
        if (!addEndpoint) return;

        btn.setAttribute('disabled', 'true');
        btn.textContent = 'Adding…';

        try {
            const form = new FormData();
            form.set('user_id', String(idNum));

            const res = await fetch(addEndpoint, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                },
                body: form,
            });

            if (!res.ok) throw new Error('bad_status');

            const row = results.querySelector(`[data-member-row-id="${idNum}"]`);
            if (row) row.remove();
            setStatus('Member added.');
        } catch {
            btn.removeAttribute('disabled');
            btn.textContent = 'Add';
            setStatus('Failed to add member.');
        }
    });

    setStatus('Type at least 2 characters to search.');
}

function initClubSessionSetup() {
    const root = document.querySelector('[data-club-session-setup="true"]');
    if (!root) return;

    const allBtn = root.querySelector('[data-session-select-all="true"]');
    const noneBtn = root.querySelector('[data-session-select-none="true"]');

    const setAll = (checked) => {
        const boxes = root.querySelectorAll('[data-session-member-checkbox="true"]');
        boxes.forEach((b) => {
            if (b instanceof HTMLInputElement) b.checked = checked;
        });
    };

    if (allBtn) {
        allBtn.addEventListener('click', () => setAll(true));
    }
    if (noneBtn) {
        noneBtn.addEventListener('click', () => setAll(false));
    }
}

function initClubSessionLobby() {
    const root = document.querySelector('[data-club-session-lobby="true"]');
    if (!root) return;

    const lobbyEndpoint = root.getAttribute('data-lobby-endpoint') || '';
    const joinEndpoint = root.getAttribute('data-join-endpoint') || '';
    const leaveEndpoint = root.getAttribute('data-leave-endpoint') || '';
    const kickEndpoint = root.getAttribute('data-kick-endpoint') || '';
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const statusEl = root.querySelector('[data-session-lobby-status="true"]');
    const joinBtn = root.querySelector('[data-session-join="true"]');
    const leaveBtn = root.querySelector('[data-session-leave="true"]');
    const listEl = root.querySelector('[data-session-joined-list="true"]');
    const countEl = root.querySelector('[data-session-count="true"]');
    const startBtn = root.querySelector('[data-session-start="true"]');

    const setStatus = (t) => {
        if (statusEl) statusEl.textContent = t || '';
    };

    const escapeHtml = (t) => String(t || '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const render = (payload) => {
        const active = !!payload?.active;
        if (active) {
            setStatus('Session already started.');
            if (joinBtn) joinBtn.classList.add('hidden');
            if (leaveBtn) leaveBtn.classList.add('hidden');
            if (startBtn) startBtn.setAttribute('disabled', 'true');
            if (listEl) listEl.innerHTML = `<div class="px-4 py-4 text-sm text-slate-300">Session already active.</div>`;
            if (countEl) countEl.textContent = '';
            return;
        }

        const joined = !!payload?.joined;
        const isAdmin = !!payload?.is_admin;
        const items = Array.isArray(payload?.items) ? payload.items : [];

        setStatus(joined ? 'You have joined today’s session.' : 'You have not joined yet.');
        if (joinBtn) joinBtn.classList.toggle('hidden', joined);
        if (leaveBtn) leaveBtn.classList.toggle('hidden', !joined);

        if (countEl) countEl.textContent = `(${items.length})`;

        if (startBtn) {
            if (isAdmin && items.length >= 2) startBtn.removeAttribute('disabled');
            else startBtn.setAttribute('disabled', 'true');
        }

        if (!listEl) return;
        if (items.length === 0) {
            listEl.innerHTML = `<div class="px-4 py-4 text-sm text-slate-300">No one joined yet.</div>`;
            return;
        }

        listEl.innerHTML = items.map((u) => {
            const id = Number.parseInt(String(u.user_id || '0'), 10);
            const name = escapeHtml(u.name);
            const email = escapeHtml(u.email);
            const kick = isAdmin ? `<button type="button" class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15" data-session-kick-user="${id}">Remove</button>` : '';
            return `
<div class="flex items-center justify-between gap-3 px-4 py-3" data-session-row="${id}">
  <div class="min-w-0">
    <div class="text-sm font-semibold text-white truncate">${name}</div>
    <div class="mt-1 text-xs text-slate-400 truncate">${email}</div>
  </div>
  ${kick}
</div>`;
        }).join('');
    };

    const load = async () => {
        if (!lobbyEndpoint) return;
        try {
            const res = await fetch(lobbyEndpoint, { headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error('bad_status');
            const data = await res.json();
            render(data);
        } catch {
            setStatus('Failed to load lobby.');
        }
    };

    // Expose for websocket-driven refresh.
    window.__clubLobbyReload = load;

    const post = async (url, bodyObj) => {
        const form = new FormData();
        Object.entries(bodyObj || {}).forEach(([k, v]) => form.set(k, String(v)));
        const res = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
            body: form,
        });
        if (!res.ok) throw new Error('bad_status');
        return res.json().catch(() => ({}));
    };

    if (joinBtn) {
        joinBtn.addEventListener('click', async () => {
            joinBtn.setAttribute('disabled', 'true');
            try {
                await post(joinEndpoint, {});
                await load();
            } catch {
                setStatus('Failed to join.');
            } finally {
                joinBtn.removeAttribute('disabled');
            }
        });
    }

    if (leaveBtn) {
        leaveBtn.addEventListener('click', async () => {
            leaveBtn.setAttribute('disabled', 'true');
            try {
                await post(leaveEndpoint, {});
                await load();
            } catch {
                setStatus('Failed to leave.');
            } finally {
                leaveBtn.removeAttribute('disabled');
            }
        });
    }

    if (listEl) {
        listEl.addEventListener('click', async (e) => {
            const target = e.target instanceof Element ? e.target : null;
            if (!target) return;
            const btn = target.closest('[data-session-kick-user]');
            if (!btn) return;
            const userId = btn.getAttribute('data-session-kick-user') || '';
            const id = Number.parseInt(userId, 10);
            if (!Number.isFinite(id) || id <= 0) return;
            if (!kickEndpoint) return;

            btn.setAttribute('disabled', 'true');
            try {
                await post(kickEndpoint, { user_id: id });
                await load();
            } catch {
                setStatus('Failed to remove.');
            } finally {
                btn.removeAttribute('disabled');
            }
        });
    }

    load();
}

function initClubRoomState() {
    const root = document.querySelector('[data-club-room="true"]');
    if (!root) return;

    const stateEndpoint = root.getAttribute('data-club-state-endpoint') || '';
    if (!stateEndpoint) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const pendingList = root.querySelector('[data-pending-requests-list="true"]');

    const escapeHtml = (t) => String(t || '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');

    const renderPending = (items) => {
        if (!pendingList) return;
        if (!Array.isArray(items) || items.length === 0) {
            pendingList.innerHTML = `<div class="px-4 py-4 text-sm text-slate-300">No pending requests.</div>`;
            return;
        }

        pendingList.innerHTML = items.map((r) => {
            const name = escapeHtml(r?.name || '—');
            const email = escapeHtml(r?.email || '');
            const approveUrl = escapeHtml(r?.approve_url || '');
            const rejectUrl = escapeHtml(r?.reject_url || '');
            return `
<div class="border-b border-white/10 px-4 py-3 last:border-b-0">
  <div class="flex items-center justify-between gap-3">
    <div class="min-w-0">
      <div class="text-sm font-semibold text-white truncate">${name}</div>
      <div class="mt-1 text-xs text-slate-400">${email}</div>
    </div>
    <div class="flex items-center gap-2">
      <form method="POST" action="${approveUrl}" data-club-ajax-form="true">
        <input type="hidden" name="_token" value="${escapeHtml(csrf)}">
        <input type="hidden" name="_method" value="PATCH">
        <button class="bg-indigo-500 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-400">Approve</button>
      </form>
      <form method="POST" action="${rejectUrl}" data-club-ajax-form="true">
        <input type="hidden" name="_token" value="${escapeHtml(csrf)}">
        <input type="hidden" name="_method" value="PATCH">
        <button class="bg-white/10 px-3 py-2 text-xs font-semibold text-white hover:bg-white/15">Reject</button>
      </form>
    </div>
  </div>
</div>`;
        }).join('');
    };

    const refresh = async () => {
        try {
            const res = await fetch(stateEndpoint, { headers: { Accept: 'application/json' } });
            if (!res.ok) throw new Error('bad_status');
            const data = await res.json();
            renderPending(data?.pending_requests || []);
        } catch {
            // ignore
        }
    };

    window.__clubRoomRefresh = refresh;
}

function initClubAddPointAjax() {
    // Intercept +1 forms so page doesn't refresh
    document.addEventListener('submit', async (e) => {
        const form = e.target instanceof HTMLFormElement ? e.target : null;
        if (!form) return;
        if (!form.matches('[data-club-add-point-form="true"]')) return;

        e.preventDefault();

        const action = form.getAttribute('action') || '';
        if (!action) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const btn = form.querySelector('button[type="submit"]');
        if (btn) btn.setAttribute('disabled', 'true');

        try {
            const body = new FormData(form);
            const res = await fetch(action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                    Accept: 'application/json',
                },
                body,
            });
            if (!res.ok) throw new Error('bad_status');
            const data = await res.json();

            const userId = Number.parseInt(String(data?.userId || form.getAttribute('data-club-add-point-user-id') || '0'), 10);
            const points = Number.parseInt(String(data?.points || '0'), 10);
            if (Number.isFinite(userId) && userId > 0 && Number.isFinite(points) && points >= 0) {
                const el = document.querySelector(`[data-points-for-user-id="${userId}"]`);
                if (el) el.textContent = String(points);
            }
        } catch {
            // ignore (existing realtime may still update)
        } finally {
            if (btn) btn.removeAttribute('disabled');
        }
    });
}

function initClubAjaxForms() {
    document.addEventListener('submit', async (e) => {
        const form = e.target instanceof HTMLFormElement ? e.target : null;
        if (!form) return;
        if (!form.matches('[data-club-ajax-form="true"]')) return;

        e.preventDefault();

        const msg = form.getAttribute('data-club-ajax-confirm');
        if (msg && !window.confirm(msg)) return;

        const action = form.getAttribute('action') || '';
        if (!action) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        const btn = form.querySelector('button');
        if (btn) btn.setAttribute('disabled', 'true');

        try {
            const body = new FormData(form);
            const res = await fetch(action, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' },
                body,
            });
            if (!res.ok) throw new Error('bad_status');
            const data = await res.json().catch(() => ({}));

            const success = form.getAttribute('data-club-ajax-success') || '';
            if (success === 'redirect') {
                const url = form.getAttribute('data-club-redirect-url') || data?.redirect || '';
                if (url) window.location.href = url;
                return;
            }

            if (typeof window.__clubRoomRefresh === 'function') {
                window.__clubRoomRefresh();
            }
        } catch {
            // ignore
        } finally {
            if (btn) btn.removeAttribute('disabled');
        }
    });
}

// Clubs realtime (WebSockets via pusher-js, no Laravel Echo)
async function initClubsRealtime() {
    const root = document.querySelector('[data-club-realtime]');
    if (!root) return;

    const enabled = root.getAttribute('data-club-realtime') === '1';
    if (!enabled) return;

    const clubId = Number.parseInt(root.getAttribute('data-club-id') || '0', 10);
    if (!Number.isFinite(clubId) || clubId <= 0) return;

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Lazy import to avoid loading on non-club pages
    const mod = await import('pusher-js');
    const Pusher = mod.default;

    const key = import.meta.env.VITE_REVERB_APP_KEY;
    const wsHost = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
    const wsPort = Number.parseInt(import.meta.env.VITE_REVERB_PORT || '80', 10);
    const scheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

    if (!key) return;

    const pusher = new Pusher(key, {
        wsHost,
        wsPort: scheme === 'https' ? undefined : wsPort,
        wssPort: scheme === 'https' ? wsPort : undefined,
        forceTLS: scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        disableStats: true,
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': csrf,
            },
        },
    });

    const channel = pusher.subscribe(`private-club.${clubId}`);

    // Refresh state without full page reload.
    const refreshAll = () => {
        if (typeof window.__clubLobbyReload === 'function') window.__clubLobbyReload();
        if (typeof window.__clubRoomRefresh === 'function') window.__clubRoomRefresh();
    };

    channel.bind('club.session_started', refreshAll);
    channel.bind('club.session_ended', refreshAll);
    channel.bind('club.state_changed', refreshAll);

    channel.bind('club.master_changed', (payload) => {
        const nameEl = document.querySelector('[data-current-master-name="true"]');
        if (nameEl) {
            nameEl.textContent = payload?.currentMasterName || 'Master';
            if (payload?.currentMasterUserId) {
                nameEl.setAttribute('data-current-master-user-id', String(payload.currentMasterUserId));
            }
        }

        const badges = document.querySelectorAll('[data-master-badge-for-user-id]');
        badges.forEach((b) => b.classList.add('hidden'));
        if (payload?.currentMasterUserId) {
            const active = document.querySelector(`[data-master-badge-for-user-id="${payload.currentMasterUserId}"]`);
            if (active) active.classList.remove('hidden');
        }
    });

    channel.bind('club.point_added', (payload) => {
        const userId = Number.parseInt(payload?.userId || '0', 10);
        const points = Number.parseInt(payload?.points || '0', 10);
        if (!Number.isFinite(userId) || userId <= 0) return;
        if (!Number.isFinite(points) || points < 0) return;

        const el = document.querySelector(`[data-points-for-user-id="${userId}"]`);
        if (el) el.textContent = String(points);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initClubsRealtime().catch(() => {});
    initClubMemberSearch();
    initClubSessionSetup();
    initClubSessionLobby();
    initClubAddPointAjax();
    initClubAjaxForms();
    initClubRoomState();
});

// Push notifications (FCM)
import { wirePushEnableButtons } from './fcm_client.js';
document.addEventListener('DOMContentLoaded', () => {
    wirePushEnableButtons();
});


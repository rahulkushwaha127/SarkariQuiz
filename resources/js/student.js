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

document.addEventListener('click', (e) => {
    const target = e.target instanceof Element ? e.target : null;
    if (!target) return;

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
    closeStudentSidebar();
});

// Quiz countdown timer (optional)
function formatTime(seconds) {
    const s = Math.max(0, Math.floor(seconds));
    const m = Math.floor(s / 60);
    const r = s % 60;
    return `${m}:${String(r).padStart(2, '0')}`;
}

function initQuizTimer() {
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
            // Auto-submit the page form if present (server will handle expiry too)
            const form = document.querySelector('[data-quiz-autosubmit="true"]');
            if (form && form instanceof HTMLFormElement) {
                form.submit();
            }
        }
    };

    tick();
    window.setInterval(tick, 500);
}

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


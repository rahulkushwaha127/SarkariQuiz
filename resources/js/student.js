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


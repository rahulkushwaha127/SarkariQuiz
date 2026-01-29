function buildToastElement(message, type = 'success') {
    const colors = {
        success: {
            bg: 'bg-green-600',
            ring: 'ring-green-500/30',
            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>'
        },
        error: {
            bg: 'bg-red-600',
            ring: 'ring-red-500/30',
            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
        },
        info: {
            bg: 'bg-blue-600',
            ring: 'ring-blue-500/30',
            icon: '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a6 6 0 110-12 6 6 0 010 12z"/></svg>'
        }
    }[type] || colors.success;

    const wrapper = document.createElement('div');
    wrapper.className = `pointer-events-auto text-white ${colors.bg} ring-1 ${colors.ring} shadow-xl rounded-xl px-4 py-3 flex items-start gap-3 translate-y-[-8px] opacity-0 transition-all duration-200`;
    wrapper.innerHTML = `
        <div class="shrink-0">${colors.icon}</div>
        <div class="text-sm leading-5">${message}</div>
        <button type="button" class="ml-2 opacity-80 hover:opacity-100">&times;</button>
    `;

    const closeBtn = wrapper.querySelector('button');
    closeBtn.addEventListener('click', () => dismiss(wrapper));

    // Enter animation
    requestAnimationFrame(() => {
        wrapper.classList.remove('translate-y-[-8px]', 'opacity-0');
        wrapper.classList.add('translate-y-0', 'opacity-100');
    });

    // Auto dismiss
    setTimeout(() => dismiss(wrapper), 4000);
    return wrapper;
}

function dismiss(el) {
    el.classList.add('opacity-0', '-translate-y-2');
    setTimeout(() => el.remove(), 150);
}

export function showToast({ message, type = 'success' }) {
    if (!message) return;
    const root = document.getElementById('toast-root');
    if (!root) return;
    const toastEl = buildToastElement(message, type);
    root.appendChild(toastEl);
}

// Global helper and event listener
window.toast = (message, type = 'success') => showToast({ message, type });
window.addEventListener('toast', (e) => showToast(e.detail || {}));

// Bootstrap from flash data
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.getElementById('toast-flash');
    if (!flash) return;
    const success = flash.getAttribute('data-success');
    const info = flash.getAttribute('data-info');
    const error = flash.getAttribute('data-error');
    const errors = flash.getAttribute('data-errors');
    if (success) showToast({ message: success, type: 'success' });
    if (info) showToast({ message: info, type: 'info' });
    if (error) showToast({ message: error, type: 'error' });
    if (errors) {
        const first = errors.split('||')[0];
        if (first) showToast({ message: first, type: 'error' });
    }
});



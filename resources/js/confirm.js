function showConfirm(message, confirmText = 'Delete', confirmColor = 'red') {
    return new Promise((resolve) => {
        const modal = document.getElementById('confirm-modal');
        const backdrop = document.getElementById('confirm-backdrop');
        const msg = document.getElementById('confirm-message');
        const ok = document.getElementById('confirm-ok');
        const cancel = document.getElementById('confirm-cancel');
        if (!modal || !backdrop || !msg || !ok || !cancel) {
            const result = window.confirm(message);
            resolve(result);
            return;
        }

        msg.textContent = message || 'Are you sure?';
        ok.textContent = confirmText || ok.textContent;
        
        // Update button color based on action
        // Remove existing color classes
        const allClasses = Array.from(ok.classList);
        allClasses.forEach(cls => {
            if ((cls.startsWith('bg-') || cls.startsWith('hover:bg-')) && 
                /(red|green|blue|yellow|gray)-\d+/.test(cls)) {
                ok.classList.remove(cls);
            }
        });
        
        // Add appropriate color classes
        if (confirmColor === 'green') {
            ok.classList.add('bg-green-600', 'hover:bg-green-700');
        } else {
            ok.classList.add('bg-red-600', 'hover:bg-red-700');
        }

        const open = () => {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            backdrop.classList.remove('hidden');
        };
        const close = () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            backdrop.classList.add('hidden');
            ok.removeEventListener('click', onOk);
            cancel.removeEventListener('click', onCancel);
        };
        const onOk = () => { close(); resolve(true); };
        const onCancel = () => { close(); resolve(false); };

        ok.addEventListener('click', onOk);
        cancel.addEventListener('click', onCancel);
        open();
    });
}

// Intercept forms with data-confirm
document.addEventListener('submit', async (e) => {
    const form = e.target;
    if (!(form instanceof HTMLFormElement)) return;
    const message = form.getAttribute('data-confirm');
    if (!message) return;

    // Avoid loop: allow once when data-confirmed="1"
    if (form.getAttribute('data-confirmed') === '1') return;
    e.preventDefault();
    const confirmText = form.getAttribute('data-confirm-ok') || 'Delete';
    const confirmColor = form.getAttribute('data-confirm-color') || 'red';
    const ok = await showConfirm(message, confirmText, confirmColor);
    if (ok) {
        form.setAttribute('data-confirmed', '1');
        form.submit();
    }
});

// Expose for ad-hoc usage
window.confirmDialog = showConfirm;



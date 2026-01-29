import './bootstrap';

import Alpine from 'alpinejs';
import './async-list';
import './multi-select';
import './toast';
import './confirm';

window.Alpine = Alpine;

// Theme Management
document.addEventListener('DOMContentLoaded', function() {
    // Get saved theme from localStorage or default to 'light'
    const savedTheme = localStorage.getItem('theme') || 'light';
    const html = document.documentElement;
    
    // Apply saved theme
    if (savedTheme === 'dark') {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
    
    // Theme toggle function
    window.toggleTheme = function() {
        html.classList.toggle('dark');
        const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
        localStorage.setItem('theme', currentTheme);
    };
    
    // Language switcher
    window.changeLanguage = function(locale) {
        const form = document.getElementById('language-form');
        if (form) {
            document.getElementById('language-input').value = locale;
            form.submit();
        }
    };

    // AJAX modal loader
    document.body.addEventListener('click', async (e) => {
        const trigger = e.target.closest('[data-modal-url]');
        if (!trigger) return;
        e.preventDefault();
        const url = trigger.getAttribute('data-modal-url');
        if (!url) return;
        try {
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await res.text();
            const container = document.getElementById('ajax-modal-body');
            if (container) {
                // Inject HTML
                container.innerHTML = html;

                // Execute any inline <script> tags inside the injected HTML
                const scripts = Array.from(container.querySelectorAll('script'));
                scripts.forEach((oldScript) => {
                    const newScript = document.createElement('script');
                    // Copy attributes (type, src, etc.)
                    Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                    if (oldScript.src) {
                        newScript.src = oldScript.src;
                    } else {
                        newScript.textContent = oldScript.textContent || '';
                    }
                    // Append to body to ensure execution
                    document.body.appendChild(newScript);
                });

                // Dispatch events on both window and document for compatibility
                window.dispatchEvent(new CustomEvent('modal-content-loaded'));
                document.dispatchEvent(new CustomEvent('modal-content-loaded'));

                // Small delay before opening modal to ensure content is rendered
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('open-modal', { detail: 'ajax-modal' }));
                }, 50);
            }
        } catch (err) {
            console.error('Failed to load modal content', err);
        }
    });
});

Alpine.start();

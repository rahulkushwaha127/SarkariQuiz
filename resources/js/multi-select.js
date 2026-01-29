import Choices from 'choices.js/public/assets/scripts/choices.js';
import 'choices.js/public/assets/styles/choices.css';

// Store instances to prevent re-initialization
const choicesInstances = new WeakMap();

function initMultiSelects(container = document) {
    const selects = container.querySelectorAll('select[multiple], select[data-multi-select]');
    
    selects.forEach(select => {
        // Skip if already initialized
        if (select.hasAttribute('data-choices-instance')) return;
        
        // Ensure select has options
        if (select.options.length === 0) {
            return;
        }
        
        // Ensure select is in DOM
        if (!select.isConnected) return;
        
        const placeholder = select.getAttribute('data-placeholder') || 'Select options...';
        const maxItems = select.hasAttribute('data-max-items') ? parseInt(select.getAttribute('data-max-items')) : null;
        
        try {
            const instance = new Choices(select, {
                removeItemButton: true,
                placeholder: true,
                placeholderValue: placeholder,
                searchEnabled: true,
                searchChoices: true,
                itemSelectText: '',
                maxItemCount: maxItems,
                shouldSort: true,
                position: 'bottom',
                classNames: {
                    containerOuter: 'choices',
                    containerInner: 'choices__inner',
                    input: 'choices__input',
                    inputCloned: 'choices__input--cloned',
                    list: 'choices__list',
                    listItems: 'choices__list--multiple',
                    listSingle: 'choices__list--single',
                    listDropdown: 'choices__list--dropdown',
                    item: 'choices__item',
                    itemSelectable: 'choices__item--selectable',
                    itemDisabled: 'choices__item--disabled',
                    itemChoice: 'choices__item--choice',
                    placeholder: 'choices__placeholder',
                    group: 'choices__group',
                    groupHeading: 'choices__heading',
                    button: 'choices__button',
                    activeState: 'is-active',
                    focusState: 'is-focused',
                    openState: 'is-open',
                    disabledState: 'is-disabled',
                    highlightedState: 'is-highlighted',
                    selectedState: 'is-selected',
                    flippedState: 'is-flipped',
                    loadingState: 'is-loading',
                    noResults: 'has-no-results',
                    noChoices: 'has-no-choices'
                }
            });
            
            // Mark as initialized
            select.setAttribute('data-choices-instance', 'true');
            choicesInstances.set(select, instance);
        } catch (e) {
            console.error('Failed to initialize Choices:', e, select);
        }
    });
}

// Initialize on page load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => initMultiSelects());
} else {
    initMultiSelects();
}

// Re-initialize when modals/content is loaded dynamically
document.addEventListener('modal-content-loaded', () => {
    // Try multiple times to catch different rendering states
    [100, 300, 500].forEach(delay => {
        setTimeout(() => {
            const modal = document.getElementById('ajax-modal-body');
            if (modal) initMultiSelects(modal);
        }, delay);
    });
});

// Also listen for modal open event
window.addEventListener('open-modal', () => {
    setTimeout(() => {
        const modal = document.getElementById('ajax-modal-body');
        if (modal) initMultiSelects(modal);
    }, 150);
});

// Also watch for dynamic content via MutationObserver
const observer = new MutationObserver(mutations => {
    mutations.forEach(mutation => {
        mutation.addedNodes.forEach(node => {
            if (node.nodeType === 1) { // Element node
                initMultiSelects(node);
            }
        });
    });
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});

export { initMultiSelects };
// Permission checkboxes handler with group selection
export function initPermissionCheckboxes() {
    console.log('🔵 initPermissionCheckboxes called');
    alert('initPermissionCheckboxes called');
    
    const selectAllMaster = document.getElementById('select-all-permissions');
    console.log('Master checkbox found:', selectAllMaster);
    
    if (!selectAllMaster) {
        console.warn('⚠️ Master checkbox not found, exiting');
        alert('Master checkbox not found');
        return;
    }
    
    alert('Master checkbox found!');
    let currentMasterCheckbox = selectAllMaster;
    
    // Helper functions
    function updateMasterCheckbox() {
        const master = document.getElementById('select-all-permissions');
        if (!master) return;
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(cb => cb.checked);
        const someChecked = Array.from(allCheckboxes).some(cb => cb.checked);
        master.checked = allChecked;
        master.indeterminate = someChecked && !allChecked;
    }

    function updateGroupCheckbox(groupName) {
        const groupCheckbox = document.querySelector(`.group-select-all[data-group="${groupName}"]`);
        if (!groupCheckbox) return;
        const groupCheckboxes = Array.from(document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`));
        if (groupCheckboxes.length === 0) return;
        const allChecked = groupCheckboxes.every(cb => cb.checked);
        const someChecked = groupCheckboxes.some(cb => cb.checked);
        groupCheckbox.checked = allChecked;
        groupCheckbox.indeterminate = someChecked && !allChecked;
    }

    // Master "Select All" functionality
    function handleMasterChange() {
        console.log('🔵 Master checkbox changed:', this.checked);
        alert('Master checkbox changed: ' + this.checked);
        const isChecked = this.checked;
        const groupCheckboxes = document.querySelectorAll('.group-select-all');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        console.log('Group checkboxes found:', groupCheckboxes.length);
        console.log('Permission checkboxes found:', permissionCheckboxes.length);
        alert('Group checkboxes: ' + groupCheckboxes.length + ', Permission checkboxes: ' + permissionCheckboxes.length);
        
        groupCheckboxes.forEach(cb => {
            cb.checked = isChecked;
            cb.indeterminate = false;
        });
        permissionCheckboxes.forEach(cb => {
            cb.checked = isChecked;
        });
        console.log('✅ All checkboxes updated');
    }
    
    // Remove old listener if exists and add new one
    const newSelectAll = currentMasterCheckbox.cloneNode(true);
    currentMasterCheckbox.parentNode.replaceChild(newSelectAll, currentMasterCheckbox);
    newSelectAll.addEventListener('change', handleMasterChange);
    currentMasterCheckbox = newSelectAll;

    // Group "Select All" functionality
    const groupSelectAlls = document.querySelectorAll('.group-select-all');
    console.log('Group select all checkboxes found:', groupSelectAlls.length);
    alert('Group select all checkboxes found: ' + groupSelectAlls.length);
    
    groupSelectAlls.forEach((groupCheckbox, index) => {
        console.log(`Processing group checkbox ${index + 1}:`, groupCheckbox.dataset.group);
        const newCheckbox = groupCheckbox.cloneNode(true);
        groupCheckbox.parentNode.replaceChild(newCheckbox, groupCheckbox);
        
        newCheckbox.addEventListener('change', function() {
            const groupName = this.dataset.group;
            const isChecked = this.checked;
            console.log('🔵 Group checkbox changed:', groupName, isChecked);
            alert('Group checkbox changed: ' + groupName + ' = ' + isChecked);
            
            const groupCheckboxes = document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`);
            console.log('Permission checkboxes in group:', groupCheckboxes.length);
            alert('Permission checkboxes in group ' + groupName + ': ' + groupCheckboxes.length);
            
            groupCheckboxes.forEach(cb => {
                cb.checked = isChecked;
            });
            this.indeterminate = false;
            updateMasterCheckbox();
            console.log('✅ Group checkboxes updated');
        });
    });

    // Individual permission checkbox functionality
    const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
    console.log('Individual permission checkboxes found:', permissionCheckboxes.length);
    alert('Individual permission checkboxes found: ' + permissionCheckboxes.length);
    
    permissionCheckboxes.forEach((checkbox, index) => {
        checkbox.addEventListener('change', function() {
            console.log('🔵 Individual checkbox changed:', this.value, this.dataset.group);
            updateMasterCheckbox();
            updateGroupCheckbox(this.dataset.group);
        });
    });

    // Initialize checkboxes state on load
    document.querySelectorAll('.group-select-all').forEach(groupCheckbox => {
        updateGroupCheckbox(groupCheckbox.dataset.group);
    });
    updateMasterCheckbox();
}

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    console.log('📄 DOM still loading, waiting for DOMContentLoaded');
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📄 DOMContentLoaded fired, initializing checkboxes');
        setTimeout(initPermissionCheckboxes, 100);
    });
} else {
    console.log('📄 DOM already ready, initializing checkboxes immediately');
    setTimeout(initPermissionCheckboxes, 100);
}

// Re-initialize when modal content is loaded (listen on both window and document)
window.addEventListener('modal-content-loaded', function() {
    console.log('📦 Modal content loaded event fired (window), re-initializing checkboxes');
    alert('Modal content loaded (window) - reinitializing checkboxes');
    setTimeout(initPermissionCheckboxes, 100);
});

document.addEventListener('modal-content-loaded', function() {
    console.log('📦 Modal content loaded event fired (document), re-initializing checkboxes');
    alert('Modal content loaded (document) - reinitializing checkboxes');
    setTimeout(initPermissionCheckboxes, 100);
});

// Also try to initialize when modal opens (multiple delays like multi-select.js)
window.addEventListener('open-modal', function(e) {
    if (e.detail === 'ajax-modal') {
        console.log('📦 Modal opened, will re-initialize checkboxes');
        alert('Modal opened - will try to initialize checkboxes');
        [100, 300, 500, 700].forEach(delay => {
            setTimeout(() => {
                console.log(`📦 Trying to initialize after ${delay}ms`);
                const modal = document.getElementById('ajax-modal-body');
                if (modal) {
                    const masterCheckbox = modal.querySelector('#select-all-permissions');
                    console.log('Modal body found:', modal);
                    console.log('Master checkbox found:', masterCheckbox);
                    if (masterCheckbox) {
                        console.log('📦 Found permission form in modal, initializing');
                        alert('Found permission form, initializing!');
                        initPermissionCheckboxes();
                    } else {
                        console.log('⚠️ Master checkbox not found yet in modal');
                    }
                }
            }, delay);
        });
    }
});

// MutationObserver to watch for when content is added to modal
const modalObserver = new MutationObserver(function(mutations) {
    const modal = document.getElementById('ajax-modal-body');
    if (modal && modal.querySelector('#select-all-permissions')) {
        console.log('🔍 MutationObserver detected permission form in modal');
        alert('MutationObserver detected permission form!');
        initPermissionCheckboxes();
    }
});

// Start observing the modal container
const modalContainer = document.getElementById('ajax-modal-body');
if (modalContainer) {
    modalObserver.observe(modalContainer, {
        childList: true,
        subtree: true
    });
    console.log('🔍 Started observing modal container for changes');
}


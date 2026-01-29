<div class="p-4 sm:p-6">
    <div class="mb-4">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $title }}</h3>
    </div>
    <form method="POST" action="{{ $action }}" class="space-y-4">
        @csrf
        @if(($method ?? 'POST') === 'PUT')
            @method('PUT')
        @endif
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Name') }}</label>
            <input type="text" name="name" value="{{ old('name', $role->name) }}" class="w-full mt-1 px-3 py-2 rounded-xl border bg-white dark:bg-slate-800 text-gray-900 dark:text-white" required>
        </div>
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ __('Permissions') }}</label>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-slate-300 cursor-pointer">
                    <input type="checkbox" id="select-all-permissions" class="rounded">
                    <span>{{ __('Select All') }}</span>
                </label>
            </div>
            <div class="mt-2 space-y-4 max-h-96 overflow-y-auto custom-scrollbar p-3 rounded-xl border dark:border-slate-700">
                @foreach(($groupedPermissions ?? []) as $groupName => $permissions)
                    <div class="permission-group" data-group="{{ $groupName }}">
                        <div class="flex items-center justify-between mb-2 pb-2 border-b border-gray-200 dark:border-slate-700">
                            <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 dark:text-white cursor-pointer">
                                <input type="checkbox" class="group-select-all rounded" data-group="{{ $groupName }}">
                                <span class="capitalize">{{ ucfirst($groupName) }}</span>
                            </label>
                            <span class="text-xs text-gray-500 dark:text-slate-400">{{ $permissions->count() }} {{ __('permissions') }}</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 pl-6">
                            @foreach($permissions as $perm)
                                <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-slate-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-slate-800/50 p-1 rounded">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->name }}" class="permission-checkbox rounded" data-group="{{ $groupName }}" {{ in_array($perm->name, old('permissions', $selectedPermissions ?? [])) ? 'checked' : '' }}>
                                    <span>{{ $perm->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-2 pt-2">
            <button type="button" class="sidebar-link-inactive px-4 py-2 rounded-xl" onclick="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'ajax-modal' }))">{{ __('Cancel') }}</button>
            <button type="submit" class="btn-primary">{{ __('Save') }}</button>
        </div>
    </form>
</div>

<script>
    // Initialize permission checkboxes when this form loads
    (function() {
        function initRoleFormCheckboxes() {
            const selectAllMaster = document.getElementById('select-all-permissions');
            
            if (!selectAllMaster) {
                console.log('⚠️ Master checkbox not found');
                return;
            }
            
            console.log('✅ Initializing permission checkboxes');
            
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
                const isChecked = this.checked;
                document.querySelectorAll('.group-select-all').forEach(cb => {
                    cb.checked = isChecked;
                    cb.indeterminate = false;
                });
                document.querySelectorAll('.permission-checkbox').forEach(cb => {
                    cb.checked = isChecked;
                });
            }
            
            // Remove old listener and add new one
            const newSelectAll = selectAllMaster.cloneNode(true);
            selectAllMaster.parentNode.replaceChild(newSelectAll, selectAllMaster);
            newSelectAll.addEventListener('change', handleMasterChange);

            // Group "Select All" functionality
            document.querySelectorAll('.group-select-all').forEach(groupCheckbox => {
                const newCheckbox = groupCheckbox.cloneNode(true);
                groupCheckbox.parentNode.replaceChild(newCheckbox, groupCheckbox);
                
                newCheckbox.addEventListener('change', function() {
                    const groupName = this.dataset.group;
                    const isChecked = this.checked;
                    const groupCheckboxes = document.querySelectorAll(`.permission-checkbox[data-group="${groupName}"]`);
                    groupCheckboxes.forEach(cb => {
                        cb.checked = isChecked;
                    });
                    this.indeterminate = false;
                    updateMasterCheckbox();
                });
            });

            // Individual permission checkbox functionality
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
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

        // Initialize immediately
        initRoleFormCheckboxes();
        
        // Also initialize after a short delay to handle any async loading
        setTimeout(initRoleFormCheckboxes, 200);
    })();
</script>



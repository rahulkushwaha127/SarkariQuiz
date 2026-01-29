<div id="toast-root" class="fixed top-4 right-4 z-[99999] flex flex-col gap-2 pointer-events-none"></div>

<div id="toast-flash"
     data-success="{{ session('status') }}"
     data-info="{{ session('info') }}"
     data-error="{{ session('error') }}"
     data-errors="{{ $errors->any() ? implode('||', $errors->all()) : '' }}"
     class="hidden"></div>



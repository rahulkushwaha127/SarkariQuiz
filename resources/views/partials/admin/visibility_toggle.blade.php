@props(['url', 'active' => true])
@php
    $active = (bool) $active;
@endphp
<button type="button"
        class="visibility-toggle relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full p-0.5 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 {{ $active ? 'bg-slate-900' : 'bg-slate-300' }}"
        data-toggle-active="true"
        data-url="{{ $url }}"
        data-active="{{ $active ? '1' : '0' }}"
        aria-label="{{ $active ? 'Hide' : 'Show' }}"
        title="{{ $active ? 'Visible – click to hide' : 'Hidden – click to show' }}">
    <span class="pointer-events-none absolute left-0.5 top-1/2 h-5 w-5 -translate-y-1/2 rounded-full bg-white shadow-sm ring-0 transition-transform duration-200 {{ $active ? 'translate-x-5' : 'translate-x-0' }}"></span>
</button>

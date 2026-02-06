{{--
    Reusable on/off toggle switch.
    @param string $name    — form input name, e.g. "visibility[about]"
    @param bool   $checked — whether it should be ON
    @param bool   $small   — (optional) smaller field-level variant
--}}
@php $small = $small ?? false; @endphp

<label class="relative inline-flex shrink-0 cursor-pointer items-center{{ $small ? ' mt-6' : '' }}">
    {{-- hidden 0 so unchecked = 0 is sent --}}
    <input type="hidden" name="{{ $name }}" value="0" />
    <input type="checkbox" name="{{ $name }}" value="1"
           class="peer sr-only vis-toggle{{ $small ? ' vis-toggle-sm' : '' }}"
           @if($checked) checked @endif />
    <div class="{{ $small ? 'h-5 w-9 after:h-4 after:w-4' : 'h-6 w-11 after:h-5 after:w-5' }}
                rounded-full bg-slate-300 after:absolute after:top-0.5 after:left-[2px] after:rounded-full after:border after:border-slate-300 after:bg-white after:transition-all after:content-['']
                peer-checked:bg-indigo-600 peer-checked:after:translate-x-full peer-checked:after:border-white
                peer-focus:ring-2 peer-focus:ring-indigo-300"></div>
    @if(!$small)
        <span class="ml-2 text-xs font-medium text-slate-500 peer-checked:text-indigo-700">
            {{ $checked ? 'On' : 'Off' }}
        </span>
    @endif
</label>

{{--
    Reusable on/off toggle switch.
    @param string $name    — form input name, e.g. "visibility[about]"
    @param bool   $checked — whether it should be ON
    @param bool   $small   — (optional) smaller field-level variant
--}}
@php $small = $small ?? false; @endphp

<label class="inline-flex shrink-0 cursor-pointer items-center gap-2{{ $small ? ' mt-6' : '' }} toggle-label">
    {{-- hidden 0 so unchecked = 0 is sent --}}
    <input type="hidden" name="{{ $name }}" value="0" />
    <input type="checkbox" name="{{ $name }}" value="1"
           class="sr-only toggle-checkbox vis-toggle{{ $small ? ' vis-toggle-sm' : '' }}"
           @if($checked) checked @endif />
    <div class="toggle-track {{ $small ? 'toggle-track--sm' : 'toggle-track--lg' }}{{ $checked ? ' is-checked' : '' }}">
        <span class="toggle-knob"></span>
    </div>
    @if(!$small)
        <span class="text-xs font-medium {{ $checked ? 'text-indigo-700' : 'text-slate-500' }} toggle-label-text">
            {{ $checked ? 'On' : 'Off' }}
        </span>
    @endif
</label>

<x-mail::layout>
{{-- Header --}}
<x-slot:header>
@php
    $appName = $appSettings['app_name'] ?? config('app.name');
@endphp
<x-mail::header :url="config('app.url')">
{{ $appName }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
@php
    $footerText = $appSettings['footer_text'] ?? null;
    $appName = $appSettings['app_name'] ?? config('app.name');
    $defaultFooter = '© ' . date('Y') . ' ' . $appName . '. ' . __('All rights reserved.');
@endphp
<x-mail::footer>
{{ $footerText ?: $defaultFooter }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>

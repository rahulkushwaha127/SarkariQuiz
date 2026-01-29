@props(['url'])
@php
    $logoUrl = $appSettings['logo_light'] ?? null;
    $appName = $appSettings['app_name'] ?? config('app.name');
    $logoFullUrl = $logoUrl ? url(\Illuminate\Support\Facades\Storage::url($logoUrl)) : null;
@endphp
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($logoFullUrl)
<img src="{{ $logoFullUrl }}" class="logo" alt="{{ $appName }}" style="max-width: 100%; height: auto;">
@else
{{ $appName }}
@endif
</a>
</td>
</tr>

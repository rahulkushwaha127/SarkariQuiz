@php
    $adsEnabled = (bool) (($ads['enabled'] ?? false) && ($ads['banner_enabled'] ?? false));
@endphp

@if($adsEnabled)
    <div style="margin-top:14px;">
        @include('partials.ads.slot', ['slot' => 'public_footer', 'hide_on_question_screens' => false])
    </div>
@endif


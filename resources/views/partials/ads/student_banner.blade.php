@php
    $adsEnabled = (bool) (($ads['enabled'] ?? false) && ($ads['banner_enabled'] ?? false));
    $hideForQuestionScreens =
        request()->routeIs('play.question') ||
        request()->routeIs('practice.question');
@endphp

@if($adsEnabled && ! $hideForQuestionScreens)
    <div class="border-t border-stone-200 bg-white px-3 py-2">
        <div class="rounded-xl border border-stone-200 bg-stone-50 px-3 py-2">
            @include('partials.ads.slot', ['slot' => 'student_footer', 'hide_on_question_screens' => true])
        </div>
    </div>
@endif


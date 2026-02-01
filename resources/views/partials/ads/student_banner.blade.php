@php
    $adsEnabled = (bool) (($ads['enabled'] ?? false) && ($ads['banner_enabled'] ?? false));
    $hideForQuestionScreens =
        request()->routeIs('student.play.question') ||
        request()->routeIs('student.practice.question');
@endphp

@if($adsEnabled && ! $hideForQuestionScreens)
    <div class="border-t border-white/10 bg-slate-950/40 px-3 py-2">
        <div class="border border-white/10 bg-white/5 px-3 py-2">
            @include('partials.ads.slot', ['slot' => 'student_footer', 'hide_on_question_screens' => true])
        </div>
    </div>
@endif


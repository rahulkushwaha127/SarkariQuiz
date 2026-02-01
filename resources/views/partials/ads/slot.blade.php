@php
    /** @var string $slot */
    $slotKey = $slot ?? '';
    $hideOnQuestions = (bool) ($hide_on_question_screens ?? true);

    $html = null;
    if (is_string($slotKey) && $slotKey !== '') {
        $html = \App\Services\Ads\AdService::renderSlot($slotKey, [
            'hide_on_question_screens' => $hideOnQuestions,
        ]);
    }
@endphp

@if($html)
    {!! $html !!}
@endif


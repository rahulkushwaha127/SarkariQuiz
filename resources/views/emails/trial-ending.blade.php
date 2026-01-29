<x-mail::message>
# {{ __('Trial Ending Soon') }}

{{ __('Hello :name,', ['name' => $team->owner->name ?? $team->name]) }}

{{ __('Your :plan trial will end in :days days.', ['plan' => $plan->name, 'days' => $daysRemaining]) }}

<x-mail::panel>
**{{ __('Plan') }}:** {{ $plan->name }}  
**{{ __('Trial Ends') }}:** {{ $billing->trial_ends_at?->format('F j, Y') ?? '—' }}  
**{{ __('Days Remaining') }}:** {{ $daysRemaining }}
</x-mail::panel>

{{ __('To continue enjoying all features, please subscribe to the plan before your trial expires.') }}

<x-mail::button :url="route('billing.choose')">
{{ __('Subscribe Now') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>


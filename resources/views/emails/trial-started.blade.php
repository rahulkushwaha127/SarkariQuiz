<x-mail::message>
# {{ __('Trial Started!') }}

{{ __('Hello :name,', ['name' => $team->owner->name ?? $team->name]) }}

{{ __('Congratulations! Your :days-day trial for :plan has been activated.', ['days' => $plan->trial_days ?? 0, 'plan' => $plan->name]) }}

<x-mail::panel>
**{{ __('Plan') }}:** {{ $plan->name }}  
**{{ __('Trial Duration') }}:** {{ $plan->trial_days ?? 0 }} {{ __('days') }}  
**{{ __('Trial Ends') }}:** {{ $billing->trial_ends_at?->format('F j, Y') ?? '—' }}
</x-mail::panel>

{{ __('You now have full access to all features of this plan. Enjoy exploring!') }}

@if($plan->description)
<x-mail::panel>
{{ $plan->description }}
</x-mail::panel>
@endif

<x-mail::button :url="route('dashboard')">
{{ __('Get Started') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>


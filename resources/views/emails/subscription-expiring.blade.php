<x-mail::message>
# {{ __('Subscription Expiring Soon') }}

{{ __('Hello :name,', ['name' => $team->owner->name ?? $team->name]) }}

{{ __('Your subscription for :plan will expire in :days days.', ['plan' => $plan->name, 'days' => $daysRemaining]) }}

<x-mail::panel>
**{{ __('Plan') }}:** {{ $plan->name }}  
**{{ __('Expires On') }}:** {{ $billing->ends_at?->format('F j, Y') ?? $billing->renews_at?->format('F j, Y') ?? '—' }}  
**{{ __('Days Remaining') }}:** {{ $daysRemaining }}
</x-mail::panel>

{{ __('Renew your subscription to continue enjoying uninterrupted service.') }}

<x-mail::button :url="route('billing.choose')">
{{ __('Renew Subscription') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>


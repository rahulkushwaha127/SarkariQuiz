<x-mail::message>
# {{ __('Payment Request Approved') }}

{{ __('Hello :name,', ['name' => $team->owner->name ?? $team->name]) }}

{{ __('Great news! Your manual payment request has been approved.') }}

<x-mail::panel>
**{{ __('Invoice Number') }}:** {{ $billing->invoice_number ?? '—' }}  
**{{ __('Amount') }}:** {{ strtoupper($billing->currency ?? 'USD') }} {{ number_format(($billing->amount ?? 0) / 100, 2) }}  
**{{ __('Plan') }}:** {{ $billing->plan_code ?? '—' }}  
**{{ __('Date') }}:** {{ $billing->occurred_at?->format('F j, Y') ?? '—' }}
</x-mail::panel>

{{ __('Your subscription is now active. Thank you for your payment!') }}

<x-mail::button :url="route('billing.choose')">
{{ __('View Subscription') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>


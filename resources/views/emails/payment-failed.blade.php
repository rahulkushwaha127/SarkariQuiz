<x-mail::message>
# {{ __('Payment Failed') }}

{{ __('Hello :name,', ['name' => $team->owner->name ?? $team->name]) }}

{{ __('We encountered an issue processing your payment.') }}

<x-mail::panel>
**{{ __('Invoice Number') }}:** {{ $billing->invoice_number ?? '—' }}  
**{{ __('Amount') }}:** {{ strtoupper($billing->currency ?? 'USD') }} {{ number_format(($billing->amount ?? 0) / 100, 2) }}  
**{{ __('Plan') }}:** {{ $billing->plan_code ?? '—' }}  
**{{ __('Date') }}:** {{ $billing->occurred_at?->format('F j, Y') ?? '—' }}
</x-mail::panel>

{{ __('Please check your payment method and try again. If the problem persists, please contact our support team.') }}

<x-mail::button :url="route('billing.index')">
{{ __('Update Payment Method') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>


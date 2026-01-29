<x-mail::message>
# {{ __('Payment Request Rejected') }}

{{ __('Hello :name,', ['name' => $team->owner->name ?? $team->name]) }}

{{ __('Unfortunately, your manual payment request could not be approved at this time.') }}

<x-mail::panel>
**{{ __('Invoice Number') }}:** {{ $billing->invoice_number ?? '—' }}  
**{{ __('Amount') }}:** {{ strtoupper($billing->currency ?? 'USD') }} {{ number_format(($billing->amount ?? 0) / 100, 2) }}  
**{{ __('Plan') }}:** {{ $billing->plan_code ?? '—' }}
</x-mail::panel>

@if($reason)
<x-mail::panel>
**{{ __('Reason') }}:**  
{{ $reason }}
</x-mail::panel>
@endif

{{ __('If you have any questions or need assistance, please contact our support team.') }}

<x-mail::button :url="route('billing.choose')">
{{ __('Try Again') }}
</x-mail::button>

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>


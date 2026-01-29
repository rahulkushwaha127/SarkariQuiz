<x-mail::message>
# {{ __('Payment Successful') }}

{{ __('Hello :name,', ['name' => $team->owner->name ?? $team->name]) }}

{{ __('Your payment has been processed successfully.') }}

<x-mail::panel>
**{{ __('Invoice Number') }}:** {{ $billing->invoice_number ?? '—' }}  
**{{ __('Amount') }}:** {{ strtoupper($billing->currency ?? 'USD') }} {{ number_format(($billing->amount ?? 0) / 100, 2) }}  
**{{ __('Plan') }}:** {{ $billing->plan_code ?? '—' }}  
**{{ __('Date') }}:** {{ $billing->occurred_at?->format('F j, Y') ?? '—' }}  
**{{ __('Payment Method') }}:** {{ ucfirst($billing->provider ?? 'manual') }}
</x-mail::panel>

@if($billing->ref_url)
<x-mail::button :url="$billing->ref_url">
{{ __('View Receipt') }}
</x-mail::button>
@endif

{{ __('Thank you for your business!') }}

{{ __('Thanks,') }}<br>
{{ config('app.name') }}
</x-mail::message>


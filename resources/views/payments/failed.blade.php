@extends('layouts.student')

@section('title', 'Payment Failed')

@section('content')
<div class="flex min-h-[60vh] items-center justify-center">
    <div class="w-full max-w-md rounded-2xl border border-red-200 bg-white p-8 text-center shadow-lg">
        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
            <svg class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>
        <h1 class="mt-4 text-xl font-bold text-slate-900">Payment Failed</h1>
        <p class="mt-2 text-sm text-slate-600">
            We could not verify your payment of <strong>{{ number_format($payment->amountInRupees(), 2) }} INR</strong>.
        </p>
        <p class="mt-1 text-xs text-slate-500">If money was deducted, it will be refunded within 5-7 business days.</p>
        <div class="mt-4 rounded-xl bg-slate-50 p-3 text-left text-xs text-slate-600">
            <div class="flex justify-between py-1"><span>Reference</span><span class="font-mono">{{ $payment->gateway_order_id }}</span></div>
            <div class="flex justify-between py-1"><span>Gateway</span><span class="capitalize">{{ $payment->gateway }}</span></div>
            <div class="flex justify-between py-1"><span>Status</span><span class="font-semibold text-red-700">Failed</span></div>
        </div>
        <a href="{{ url('/') }}" class="mt-6 inline-flex items-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500">
            Go to Home
        </a>
    </div>
</div>
@endsection

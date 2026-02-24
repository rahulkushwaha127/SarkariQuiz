@extends('layouts.student')

@section('title', 'Plans')

@section('content')
@php
    $user = auth()->user();
    $isCurrentPlan = fn($plan) => $currentPlan && $currentPlan->id === $plan->id;
@endphp
<div class="space-y-6">
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-sky-600 to-indigo-600 px-5 py-5 text-white shadow-lg">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur">
            <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l2.4 7.2H22l-6 4.5 2.3 7.3L12 16.5 5.7 21l2.3-7.3-6-4.5h7.6L12 2z"/></svg>
        </div>
        <h1 class="mt-3 text-xl font-bold tracking-tight">Plans</h1>
        <p class="mt-1 text-sm text-sky-100">Unlock premium features with a subscription.</p>
        <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/10"></div>
    </div>

    {{-- Current plan banner --}}
    @if($currentPlan)
    <div class="rounded-2xl border border-sky-200 bg-sky-50 p-4">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-200">
                <svg class="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-xs font-medium text-stone-500">Active plan</div>
                <div class="flex items-center gap-2">
                    <span class="text-base font-bold text-stone-800">{{ $currentPlan->name }}</span>
                    <span class="rounded-full bg-sky-200 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-sky-700">{{ $currentPlan->durationLabel() }}</span>
                </div>
                @if(! $currentPlan->isFree())
                    <div class="mt-0.5 text-xs text-stone-600">
                        ₹{{ number_format($currentPlan->priceInRupees(), 0) }}{{ $currentPlan->durationSuffix() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- Plans listing --}}
    @if($plans->isEmpty())
        <div class="rounded-2xl border border-stone-200 bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-stone-500">No plans available yet.</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($plans as $plan)
                @php
                    $isCurrent = $isCurrentPlan($plan);
                    $isFree = $plan->isFree();
                    $isPopular = !$isFree && $plan->sort_order === $plans->where('price_paise', '>', 0)->min('sort_order');
                @endphp
                <div class="relative overflow-hidden rounded-2xl border {{ $isCurrent ? 'border-sky-300 bg-sky-50/80' : 'border-stone-200 bg-white' }} p-4 shadow-sm transition-all">
                    @if($isCurrent)
                        <div class="absolute right-3 top-3">
                            <span class="rounded-full bg-sky-600 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">Active</span>
                        </div>
                    @elseif($isPopular)
                        <div class="absolute right-3 top-3">
                            <span class="rounded-full bg-amber-500 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider text-white">Popular</span>
                        </div>
                    @endif

                    <div class="flex items-start gap-4">
                        {{-- Plan icon --}}
                        <div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl {{ $isFree ? 'bg-stone-200' : 'bg-sky-500' }}">
                            @if($isFree)
                                <svg class="h-6 w-6 text-stone-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                                </svg>
                            @else
                                <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.455 2.456L21.75 6l-1.036.259a3.375 3.375 0 00-2.455 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"/>
                                </svg>
                            @endif
                        </div>

                        {{-- Plan details --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="text-base font-bold text-stone-800">{{ $plan->name }}</h3>
                            <div class="mt-0.5 flex items-baseline gap-1">
                                @if($isFree)
                                    <span class="text-lg font-extrabold text-emerald-600">Free</span>
                                @else
                                    <span class="text-lg font-extrabold text-stone-800">₹{{ number_format($plan->priceInRupees(), 0) }}</span>
                                    <span class="text-xs text-stone-500">{{ $plan->durationSuffix() }}</span>
                                @endif
                            </div>
                            @if($plan->description)
                                <p class="mt-1 text-xs text-stone-500 line-clamp-2">{{ $plan->description }}</p>
                            @endif
                            @if($plan->price_label)
                                <span class="mt-1 inline-block text-[10px] font-medium text-stone-400">{{ $plan->price_label }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Action button --}}
                    <div class="mt-3">
                        @if($isCurrent)
                            <div class="w-full rounded-xl bg-stone-100 px-4 py-2.5 text-center text-sm font-semibold text-stone-500">
                                Current plan
                            </div>
                        @elseif($isFree)
                            <form method="POST" action="{{ route('student.subscription.activate_free') }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit"
                                        class="w-full rounded-xl border border-stone-200 bg-white px-4 py-2.5 text-sm font-semibold text-stone-700 shadow-sm hover:bg-stone-50 transition-colors">
                                    Activate free plan
                                </button>
                            </form>
                        @else
                            <button type="button"
                                    class="plan-checkout-btn w-full rounded-xl bg-sky-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-sky-500 transition-all disabled:opacity-60"
                                    data-plan-id="{{ $plan->id }}"
                                    data-plan-name="{{ $plan->name }}"
                                    data-plan-amount="{{ $plan->priceInRupees() }}"
                                    data-plan-suffix="{{ $plan->durationSuffix() }}">
                                Subscribe — ₹{{ number_format($plan->priceInRupees(), 0) }}{{ $plan->durationSuffix() }}
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Payment history --}}
    @php
        $payments = \App\Models\Payment::where('user_id', $user->id)
            ->where('purpose', 'student_plan_purchase')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
    @endphp
    @if($payments->isNotEmpty())
    <div class="rounded-2xl border border-stone-200 bg-white p-4 shadow-sm">
        <h2 class="text-sm font-semibold text-stone-800">Payment history</h2>
        <div class="mt-3 divide-y divide-stone-100">
            @foreach($payments as $p)
            <div class="flex items-center justify-between py-2.5 text-sm">
                <div>
                    <div class="font-medium text-stone-800">₹{{ number_format($p->amountInRupees(), 2) }}</div>
                    <div class="text-[10px] text-stone-500">{{ $p->created_at->format('d M Y, h:i A') }}</div>
                </div>
                <div class="text-right">
                    @if($p->status === 'paid')
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">Paid</span>
                    @elseif($p->status === 'failed')
                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">Failed</span>
                    @else
                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold text-amber-700">{{ ucfirst($p->status) }}</span>
                    @endif
                    <div class="mt-0.5 text-[10px] capitalize text-stone-400">{{ $p->gateway }}</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Status toast --}}
<div id="payment-status-msg" class="fixed inset-x-0 top-4 z-50 mx-auto hidden max-w-sm px-4">
    <div id="payment-status-inner" class="rounded-2xl border px-4 py-3 text-sm font-semibold shadow-lg"></div>
</div>

@push('scripts')
{{-- Razorpay checkout.js --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function() {
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var activeGateway = @json($activeGateway);

    document.querySelectorAll('.plan-checkout-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var planId = btn.getAttribute('data-plan-id');
            var planName = btn.getAttribute('data-plan-name');
            var amount = parseFloat(btn.getAttribute('data-plan-amount'));
            var suffix = btn.getAttribute('data-plan-suffix');

            btn.disabled = true;
            btn.textContent = 'Processing…';

            fetch('{{ route("payments.initiate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    purpose: 'student_plan_purchase',
                    purpose_id: parseInt(planId),
                    gateway: activeGateway
                })
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (!res.ok) {
                    showStatus('error', res.error || 'Payment initiation failed.');
                    resetBtn(btn);
                    return;
                }

                if (res.gateway === 'phonepe' && res.redirect_url) {
                    window.location.href = res.redirect_url;
                    return;
                }

                if (res.gateway === 'razorpay' && res.gateway_data) {
                    openRazorpay(res, btn);
                    return;
                }

                showStatus('error', 'Unknown gateway response.');
                resetBtn(btn);
            })
            .catch(function() {
                showStatus('error', 'Network error. Please try again.');
                resetBtn(btn);
            });
        });
    });

    function openRazorpay(res, btn) {
        var gd = res.gateway_data;
        var options = {
            key: gd.key_id,
            amount: gd.amount,
            currency: gd.currency,
            name: gd.name,
            description: gd.description,
            order_id: gd.order_id,
            prefill: gd.prefill || {},
            handler: function(response) {
                btn.textContent = 'Verifying…';
                fetch('{{ route("payments.razorpay.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        payment_id: res.payment_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_signature: response.razorpay_signature
                    })
                })
                .then(function(r) { return r.json(); })
                .then(function(verifyRes) {
                    if (verifyRes.ok) {
                        showStatus('success', 'Payment successful! Plan activated.');
                        setTimeout(function() { window.location.reload(); }, 1500);
                    } else {
                        showStatus('error', verifyRes.error || 'Verification failed.');
                        resetBtn(btn);
                    }
                })
                .catch(function() {
                    showStatus('error', 'Verification network error.');
                    resetBtn(btn);
                });
            },
            modal: {
                ondismiss: function() {
                    resetBtn(btn);
                }
            },
            theme: {
                color: '#6366f1'
            }
        };

        var rzp = new Razorpay(options);
        rzp.on('payment.failed', function(response) {
            showStatus('error', 'Payment failed: ' + (response.error.description || 'Unknown error'));
            resetBtn(btn);
        });
        rzp.open();
    }

    function resetBtn(btn) {
        btn.disabled = false;
        var name = btn.getAttribute('data-plan-name');
        var amount = btn.getAttribute('data-plan-amount');
        var suffix = btn.getAttribute('data-plan-suffix');
        btn.textContent = 'Subscribe — ₹' + parseInt(amount) + suffix;
    }

    function showStatus(type, message) {
        var wrap = document.getElementById('payment-status-msg');
        var inner = document.getElementById('payment-status-inner');
        wrap.classList.remove('hidden');
        if (type === 'success') {
            inner.className = 'rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800 shadow-lg';
        } else {
            inner.className = 'rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800 shadow-lg';
        }
        inner.textContent = message;
        setTimeout(function() { wrap.classList.add('hidden'); }, 6000);
    }
})();
</script>
@endpush
@endsection

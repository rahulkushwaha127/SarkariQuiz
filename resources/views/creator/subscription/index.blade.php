@extends('layouts.creator')

@section('title', 'Subscription')

@section('content')
@php
    $user = auth()->user();
    $isCurrentPlan = fn($plan) => $currentPlan && $currentPlan->id === $plan->id;
@endphp
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-semibold tracking-tight text-slate-900">Subscription</h1>
        <p class="mt-1 text-sm text-slate-600">Choose a plan that fits your needs. Upgrade or switch anytime.</p>
    </div>

    {{-- Current plan summary --}}
    @if($currentPlan)
    <div class="rounded-2xl border border-indigo-200 bg-indigo-50 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">Current plan</h2>
                <div class="mt-1 flex items-center gap-2">
                    <span class="text-lg font-bold text-indigo-700">{{ $currentPlan->name }}</span>
                    <span class="rounded-full bg-indigo-100 px-2 py-0.5 text-xs font-semibold text-indigo-700">{{ $currentPlan->durationLabel() }}</span>
                    @if($currentPlan->price_label)
                        <span class="text-sm text-slate-500">{{ $currentPlan->price_label }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
            <div class="rounded-xl border border-white bg-white p-3">
                <div class="text-xs text-slate-500">Quizzes</div>
                <div class="mt-1 text-sm font-bold text-slate-900">{{ $usage['quizzes'] ?? 0 }} / {{ $currentPlan->limitLabel('max_quizzes') }}</div>
                @if(! $currentPlan->isUnlimited('max_quizzes'))
                    @php $pct = min(100, round(($usage['quizzes'] / max(1, $currentPlan->max_quizzes)) * 100)); @endphp
                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : 'bg-indigo-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                @endif
            </div>
            <div class="rounded-xl border border-white bg-white p-3">
                <div class="text-xs text-slate-500">Batches</div>
                <div class="mt-1 text-sm font-bold text-slate-900">{{ $usage['batches'] ?? 0 }} / {{ $currentPlan->limitLabel('max_batches') }}</div>
                @if(! $currentPlan->isUnlimited('max_batches'))
                    @php $pct = min(100, round(($usage['batches'] / max(1, $currentPlan->max_batches)) * 100)); @endphp
                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : 'bg-indigo-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                @endif
            </div>
            <div class="rounded-xl border border-white bg-white p-3">
                <div class="text-xs text-slate-500">Students/batch</div>
                <div class="mt-1 text-sm font-bold text-slate-900">{{ $currentPlan->limitLabel('max_students_per_batch') }}</div>
            </div>
            <div class="rounded-xl border border-white bg-white p-3">
                <div class="text-xs text-slate-500">AI this month</div>
                <div class="mt-1 text-sm font-bold text-slate-900">{{ $usage['ai_this_month'] ?? 0 }} / {{ $currentPlan->limitLabel('max_ai_generations_per_month') }}</div>
                @if(! $currentPlan->isUnlimited('max_ai_generations_per_month'))
                    @php $pct = min(100, round(($usage['ai_this_month'] / max(1, $currentPlan->max_ai_generations_per_month)) * 100)); @endphp
                    <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full {{ $pct >= 90 ? 'bg-red-500' : 'bg-indigo-500' }}" style="width: {{ $pct }}%"></div>
                    </div>
                @endif
            </div>
        </div>
        <div class="mt-3 text-xs text-slate-500">
            Question bank: <strong class="{{ $currentPlan->can_access_question_bank ? 'text-emerald-700' : 'text-slate-600' }}">{{ $currentPlan->can_access_question_bank ? 'Yes' : 'No' }}</strong>
        </div>
    </div>
    @endif

    {{-- All plans --}}
    @if($plans->isEmpty())
        <div class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-slate-600">No plans available yet. Contact the admin.</p>
        </div>
    @else
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($plans as $plan)
                @php
                    $isCurrent = $isCurrentPlan($plan);
                    $isFree = $plan->isFree();
                @endphp
                <div class="relative flex flex-col rounded-2xl border {{ $isCurrent ? 'border-indigo-400 ring-2 ring-indigo-200' : 'border-slate-200' }} bg-white p-5 shadow-sm">
                    @if($isCurrent)
                        <div class="absolute -top-3 left-4">
                            <span class="rounded-full bg-indigo-600 px-3 py-1 text-xs font-bold text-white">Current plan</span>
                        </div>
                    @endif

                    <div class="mb-4">
                        <h3 class="text-lg font-bold text-slate-900">{{ $plan->name }}</h3>
                        <div class="mt-1 flex items-baseline gap-1">
                            @if($isFree)
                                <span class="text-2xl font-extrabold text-slate-900">Free</span>
                            @else
                                <span class="text-2xl font-extrabold text-slate-900">₹{{ number_format($plan->priceInRupees(), 0) }}</span>
                                <span class="text-sm text-slate-500">{{ $plan->durationSuffix() }}</span>
                            @endif
                        </div>
                        <span class="mt-1 inline-block rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-slate-500">{{ $plan->durationLabel() }}</span>
                        @if($plan->description)
                            <p class="mt-2 text-sm text-slate-600">{{ $plan->description }}</p>
                        @endif
                    </div>

                    <ul class="mb-5 flex-1 space-y-2 text-sm text-slate-700">
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ $plan->limitLabel('max_quizzes') }} quizzes
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ $plan->limitLabel('max_batches') }} batches
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ $plan->limitLabel('max_students_per_batch') }} students/batch
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            {{ $plan->limitLabel('max_ai_generations_per_month') }} AI generations/month
                        </li>
                        <li class="flex items-center gap-2">
                            @if($plan->can_access_question_bank)
                                <svg class="h-4 w-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="h-4 w-4 text-slate-300" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            @endif
                            Question bank access
                        </li>
                    </ul>

                    <div class="mt-auto">
                        @if($isCurrent)
                            <div class="w-full rounded-xl bg-slate-100 px-4 py-2.5 text-center text-sm font-semibold text-slate-500">
                                Current plan
                            </div>
                        @elseif($isFree)
                            <form method="POST" action="{{ route('creator.subscription.activate_free') }}">
                                @csrf
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                <button type="submit"
                                        class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                                    Activate free plan
                                </button>
                            </form>
                        @else
                            <button type="button"
                                    class="plan-checkout-btn w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-60"
                                    data-plan-id="{{ $plan->id }}"
                                    data-plan-name="{{ $plan->name }}"
                                    data-plan-amount="{{ $plan->priceInRupees() }}">
                                Get this plan — ₹{{ number_format($plan->priceInRupees(), 0) }}{{ $plan->durationSuffix() }}
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
            ->where('purpose', 'plan_purchase')
            ->orderByDesc('id')
            ->limit(10)
            ->get();
    @endphp
    @if($payments->isNotEmpty())
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-200 px-5 py-4">
            <h2 class="text-sm font-semibold text-slate-900">Payment history</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Gateway</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Txn ID</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($payments as $p)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 text-sm text-slate-700">{{ $p->created_at->format('d M Y, h:i A') }}</td>
                        <td class="px-4 py-3 text-sm font-semibold text-slate-900">₹{{ number_format($p->amountInRupees(), 2) }}</td>
                        <td class="px-4 py-3 text-sm capitalize text-slate-600">{{ $p->gateway }}</td>
                        <td class="px-4 py-3 text-sm">
                            @if($p->status === 'paid')
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-800">Paid</span>
                            @elseif($p->status === 'failed')
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">Failed</span>
                            @else
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">{{ ucfirst($p->status) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $p->gateway_payment_id ?? $p->gateway_order_id ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

{{-- Status message for payment result --}}
<div id="payment-status-msg" class="fixed inset-x-0 top-4 z-50 mx-auto hidden max-w-md">
    <div id="payment-status-inner" class="rounded-2xl border px-5 py-3 text-sm font-semibold shadow-lg"></div>
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

            btn.disabled = true;
            btn.textContent = 'Processing…';

            // Step 1: Initiate payment on backend
            fetch('{{ route("payments.initiate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    amount: amount,
                    purpose: 'plan_purchase',
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

                // PhonePe: redirect
                if (res.gateway === 'phonepe' && res.redirect_url) {
                    window.location.href = res.redirect_url;
                    return;
                }

                // Razorpay: inline checkout
                if (res.gateway === 'razorpay' && res.gateway_data) {
                    openRazorpay(res, btn);
                    return;
                }

                showStatus('error', 'Unknown gateway response.');
                resetBtn(btn);
            })
            .catch(function(err) {
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
                // Step 2: Verify payment on backend
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
                color: '#4f46e5'
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
        btn.textContent = 'Get this plan — ₹' + parseInt(amount);
    }

    function showStatus(type, message) {
        var wrap = document.getElementById('payment-status-msg');
        var inner = document.getElementById('payment-status-inner');
        wrap.classList.remove('hidden');
        if (type === 'success') {
            inner.className = 'rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 text-sm font-semibold text-emerald-800 shadow-lg';
        } else {
            inner.className = 'rounded-2xl border border-red-200 bg-red-50 px-5 py-3 text-sm font-semibold text-red-800 shadow-lg';
        }
        inner.textContent = message;
        setTimeout(function() { wrap.classList.add('hidden'); }, 6000);
    }
})();
</script>
@endpush
@endsection

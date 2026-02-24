<?php

namespace App\Http\Controllers;

use App\Events\PaymentFailed;
use App\Events\PaymentSucceeded;
use App\Events\PlanActivated;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\StudentPlan;
use App\Services\Payment\PaymentGatewayFactory;
use App\Services\ReferralService;
use App\Services\Payment\RazorpayGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Initiate a payment (called via AJAX or form).
     * Expects: amount (in rupees), purpose, purpose_id (optional).
     */
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'amount'     => ['required', 'numeric', 'min:0'],
            'purpose'    => ['required', 'string', 'max:60'],
            'purpose_id' => ['nullable', 'integer'],
            'gateway'    => ['nullable', 'string', 'in:razorpay,phonepe'],
        ]);

        // Creator plan purchase: validate purpose_id and amount
        if ($data['purpose'] === 'plan_purchase') {
            $creatorPlan = Plan::where('id', $data['purpose_id'] ?? 0)->where('is_active', true)->first();
            if (! $creatorPlan) {
                return response()->json(['ok' => false, 'error' => 'Invalid or inactive plan.'], 422);
            }
            if (! $creatorPlan->isFree() && ($creatorPlan->price_paise !== (int) round($data['amount'] * 100))) {
                return response()->json(['ok' => false, 'error' => 'Amount does not match plan price.'], 422);
            }
            if ($creatorPlan->isFree()) {
                return response()->json(['ok' => false, 'error' => 'This plan is free. Use Activate instead of payment.'], 422);
            }
        }

        // Student plan purchase: validate purpose_id and amount
        if ($data['purpose'] === 'student_plan_purchase') {
            $studentPlan = StudentPlan::where('id', $data['purpose_id'] ?? 0)->where('is_active', true)->first();
            if (! $studentPlan) {
                return response()->json(['ok' => false, 'error' => 'Invalid or inactive plan.'], 422);
            }
            if (! $studentPlan->isFree() && ($studentPlan->price_paise !== (int) round($data['amount'] * 100))) {
                return response()->json(['ok' => false, 'error' => 'Amount does not match plan price.'], 422);
            }
            if ($studentPlan->isFree()) {
                return response()->json(['ok' => false, 'error' => 'This plan is free. Use Activate instead of payment.'], 422);
            }
        }

        $gateway = $data['gateway'] ?? Setting::cachedGet('payment_active_gateway', 'razorpay');
        $amountPaise = (int) round($data['amount'] * 100);

        if ($amountPaise < 1) {
            return response()->json(['ok' => false, 'error' => 'Amount must be at least ₹1.'], 422);
        }

        // Create the payment record
        $payment = Payment::create([
            'user_id'    => Auth::id(),
            'gateway'    => $gateway,
            'amount'     => $amountPaise,
            'currency'   => 'INR',
            'status'     => 'created',
            'purpose'    => $data['purpose'],
            'purpose_id' => $data['purpose_id'] ?? null,
        ]);

        try {
            $gatewayService = PaymentGatewayFactory::make($gateway);
            $orderData = $gatewayService->createOrder($payment);

            // Update payment with gateway order ID
            $payment->update([
                'gateway_order_id' => $orderData['order_id'],
                'status'           => 'pending',
            ]);

            // For PhonePe, redirect to checkout URL
            $checkoutUrl = $gatewayService->getCheckoutUrl($payment, $orderData);
            if ($checkoutUrl) {
                return response()->json([
                    'ok'           => true,
                    'gateway'      => $gateway,
                    'redirect_url' => $checkoutUrl,
                    'payment_id'   => $payment->id,
                ]);
            }

            // For Razorpay (inline checkout), return the gateway data
            return response()->json([
                'ok'           => true,
                'gateway'      => $gateway,
                'payment_id'   => $payment->id,
                'gateway_data' => $orderData['gateway_data'],
            ]);
        } catch (\Exception $e) {
            Log::error('Payment initiation failed', [
                'payment_id' => $payment->id,
                'error'      => $e->getMessage(),
            ]);

            $payment->markFailed(['error' => $e->getMessage()]);

            return response()->json([
                'ok'    => false,
                'error' => 'Payment initiation failed. Please try again.',
            ], 500);
        }
    }

    /**
     * Verify Razorpay payment (called from JS after checkout success).
     */
    public function verifyRazorpay(Request $request)
    {
        $data = $request->validate([
            'payment_id'          => ['required', 'integer'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_order_id'   => ['required', 'string'],
            'razorpay_signature'  => ['required', 'string'],
        ]);

        $payment = Payment::where('id', $data['payment_id'])
            ->where('user_id', Auth::id())
            ->where('gateway', 'razorpay')
            ->where('status', 'pending')
            ->firstOrFail();

        $gateway = PaymentGatewayFactory::make('razorpay');
        $result = $gateway->verifyPayment($payment, $data);

        if ($result['verified']) {
            $payment->markPaid($result['payment_id'], $result['signature'], $result['meta']);

            // Dispatch any post-payment actions here
            $this->handlePostPayment($payment);

            // Fire payment success event (email/FCM/in-app notifications)
            PaymentSucceeded::dispatch($payment, $payment->user);

            return response()->json([
                'ok'      => true,
                'message' => 'Payment verified successfully.',
            ]);
        }

        $payment->markFailed($result['meta']);

        // Fire payment failed event
        PaymentFailed::dispatch($payment, $payment->user);

        return response()->json([
            'ok'    => false,
            'error' => 'Payment verification failed.',
        ], 422);
    }

    /**
     * PhonePe redirect callback after payment.
     */
    public function phonePeCallback(Request $request, Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        $gateway = PaymentGatewayFactory::make('phonepe');
        $result = $gateway->verifyPayment($payment, $request->all());

        if ($result['verified']) {
            $payment->markPaid(
                $result['payment_id'] ?? $payment->gateway_order_id,
                $result['signature'],
                $result['meta']
            );

            $this->handlePostPayment($payment);

            // Fire payment success event
            PaymentSucceeded::dispatch($payment, $payment->user);

            return redirect()->route('payments.success', ['payment' => $payment->id])
                ->with('status', 'Payment successful!');
        }

        $payment->markFailed($result['meta']);

        // Fire payment failed event
        PaymentFailed::dispatch($payment, $payment->user);

        return redirect()->route('payments.failed', ['payment' => $payment->id])
            ->with('error', 'Payment could not be verified.');
    }

    /**
     * Payment success page.
     */
    public function success(Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id(), 403);

        return view('payments.success', compact('payment'));
    }

    /**
     * Payment failed page.
     */
    public function failed(Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id(), 403);

        return view('payments.failed', compact('payment'));
    }

    /**
     * Handle post-payment logic (plan activation, etc.).
     */
    protected function handlePostPayment(Payment $payment): void
    {
        $user = $payment->user;

        // Student plan purchase (student_plans table)
        if ($payment->purpose === 'student_plan_purchase' && $payment->purpose_id) {
            $user->student_plan_id = $payment->purpose_id;
            $user->save();

            // Referral: if payer was referred, check if referrer qualifies for one-time reward
            app(ReferralService::class)->processPaymentForReferral($payment);
        }

        // Legacy: creator plan purchase (plans table) — kept for backward compatibility
        if ($payment->purpose === 'plan_purchase' && $payment->purpose_id) {
            $user->plan_id = $payment->purpose_id;
            $user->save();
        }

        // Add more post-payment handlers as needed
    }
}

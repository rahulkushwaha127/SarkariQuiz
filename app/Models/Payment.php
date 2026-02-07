<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'gateway',
        'gateway_order_id',
        'gateway_payment_id',
        'gateway_signature',
        'amount',
        'currency',
        'status',
        'purpose',
        'purpose_id',
        'meta',
    ];

    protected $casts = [
        'amount' => 'integer',
        'meta'   => 'array',
    ];

    /* ── Relationships ── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ── Helpers ── */

    public function amountInRupees(): float
    {
        return $this->amount / 100;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function markPaid(string $paymentId, ?string $signature = null, ?array $meta = null): void
    {
        $this->update([
            'status'             => 'paid',
            'gateway_payment_id' => $paymentId,
            'gateway_signature'  => $signature,
            'meta'               => $meta,
        ]);
    }

    public function markFailed(?array $meta = null): void
    {
        $this->update([
            'status' => 'failed',
            'meta'   => $meta,
        ]);
    }
}

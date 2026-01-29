<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Billing extends Model
{
    use HasFactory;

    protected $table = 'billing';

    protected $fillable = [
        'company_id', 'kind', 'plan_code', 'status', 'quantity', 'started_at', 'trial_ends_at', 'renews_at', 'canceled_at', 'ends_at',
        'type', 'amount', 'currency', 'tx_status', 'invoice_number', 'ref_url', 'occurred_at', 'provider', 'payment_method', 'notes', 'meta'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'amount' => 'integer',
        'meta' => 'array',
        'started_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'renews_at' => 'datetime',
        'canceled_at' => 'datetime',
        'ends_at' => 'datetime',
        'occurred_at' => 'datetime',
    ];

    /**
     * Get the company/team that owns this billing record.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'company_id');
    }
}



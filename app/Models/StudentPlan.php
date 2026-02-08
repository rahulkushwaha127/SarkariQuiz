<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPlan extends Model
{
    use HasFactory;

    public const DURATIONS = ['weekly', 'monthly', 'yearly'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration',
        'price_label',
        'price_paise',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_paise' => 'integer',
            'is_active'   => 'boolean',
        ];
    }

    /** Whether this plan is free (no payment). */
    public function isFree(): bool
    {
        return $this->price_paise === null || $this->price_paise <= 0;
    }

    /** Amount in rupees for display. */
    public function priceInRupees(): ?float
    {
        if ($this->isFree()) {
            return null;
        }
        return $this->price_paise / 100;
    }

    /** Human-friendly duration label. */
    public function durationLabel(): string
    {
        return match ($this->duration) {
            'weekly'  => 'Weekly',
            'yearly'  => 'Yearly',
            default   => 'Monthly',
        };
    }

    /** Duration suffix for price display (e.g. "/month"). */
    public function durationSuffix(): string
    {
        return match ($this->duration) {
            'weekly'  => '/week',
            'yearly'  => '/year',
            default   => '/month',
        };
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}

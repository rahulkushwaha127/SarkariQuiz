<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    public const DURATIONS = ['weekly', 'monthly', 'yearly'];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'duration',
        'price_label',
        'max_quizzes',
        'max_batches',
        'max_students_per_batch',
        'max_ai_generations_per_month',
        'can_access_question_bank',
        'is_default',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'max_quizzes' => 'integer',
            'max_batches' => 'integer',
            'max_students_per_batch' => 'integer',
            'max_ai_generations_per_month' => 'integer',
            'can_access_question_bank' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

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

    /**
     * Check if a given limit field is unlimited (null).
     */
    public function isUnlimited(string $field): bool
    {
        return is_null($this->getAttribute($field));
    }

    /**
     * Human-friendly label for a limit field, e.g. "10" or "Unlimited".
     */
    public function limitLabel(string $field): string
    {
        $value = $this->getAttribute($field);

        return is_null($value) ? 'Unlimited' : (string) $value;
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /* ------------------------------------------------------------------ */
    /*  Static helpers                                                     */
    /* ------------------------------------------------------------------ */

    /**
     * Return the default plan (the one assigned to new creators).
     * Falls back to the first active plan.
     */
    public static function defaultPlan(): ?self
    {
        return static::query()
            ->where('is_default', true)
            ->where('is_active', true)
            ->first()
            ?? static::query()->where('is_active', true)->orderBy('sort_order')->first();
    }
}

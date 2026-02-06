<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BatchQuiz extends Model
{
    protected $fillable = [
        'batch_id',
        'quiz_id',
        'access_mode',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Whether this quiz is currently accessible to students.
     */
    public function isAccessible(): bool
    {
        if ($this->access_mode === 'open') {
            return true;
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return false; // not started yet
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return false; // already ended
        }

        return true;
    }

    /**
     * Human-readable status label.
     */
    public function accessLabel(): string
    {
        if ($this->access_mode === 'open') {
            return 'Open';
        }

        $now = now();

        if ($this->starts_at && $now->lt($this->starts_at)) {
            return 'Upcoming';
        }

        if ($this->ends_at && $now->gt($this->ends_at)) {
            return 'Ended';
        }

        return 'Live';
    }
}

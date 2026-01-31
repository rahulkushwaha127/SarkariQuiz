<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'contest_id',
        'user_id',
        'status',
        'joined_at',
        'score',
        'time_taken_seconds',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'score' => 'integer',
        'time_taken_seconds' => 'integer',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


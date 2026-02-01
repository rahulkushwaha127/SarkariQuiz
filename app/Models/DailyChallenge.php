<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'challenge_date',
        'quiz_id',
        'created_by_user_id',
        'is_active',
    ];

    protected $casts = [
        'challenge_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}


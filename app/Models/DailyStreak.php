<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_streak',
        'best_streak',
        'last_streak_date',
    ];

    protected $casts = [
        'current_streak' => 'integer',
        'best_streak' => 'integer',
        'last_streak_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


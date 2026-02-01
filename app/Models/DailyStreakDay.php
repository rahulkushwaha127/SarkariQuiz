<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyStreakDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'streak_date',
    ];

    protected $casts = [
        'streak_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


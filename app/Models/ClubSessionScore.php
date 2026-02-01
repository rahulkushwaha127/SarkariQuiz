<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubSessionScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'points',
    ];

    protected $casts = [
        'points' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(ClubSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


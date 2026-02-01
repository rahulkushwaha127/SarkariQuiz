<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'quiz_id',
        'contest_id',
        'share_code',
        'status',
        'started_at',
        'submitted_at',
        'duration_seconds',
        'time_taken_seconds',
        'total_questions',
        'correct_count',
        'wrong_count',
        'unanswered_count',
        'score',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'duration_seconds' => 'integer',
        'time_taken_seconds' => 'integer',
        'total_questions' => 'integer',
        'correct_count' => 'integer',
        'wrong_count' => 'integer',
        'unanswered_count' => 'integer',
        'score' => 'integer',
        'share_code' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAttemptAnswer::class, 'attempt_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PyqAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'subject_id',
        'topic_id',
        'year',
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
        'share_code',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'exam_id' => 'integer',
        'subject_id' => 'integer',
        'topic_id' => 'integer',
        'year' => 'integer',
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

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function slots()
    {
        return $this->hasMany(PyqAttemptAnswer::class, 'attempt_id')->orderBy('position');
    }
}

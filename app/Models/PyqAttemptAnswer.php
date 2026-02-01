<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PyqAttemptAnswer extends Model
{
    use HasFactory;

    protected $table = 'pyq_attempt_answers';

    protected $fillable = [
        'attempt_id',
        'pyq_question_id',
        'pyq_answer_id',
        'position',
        'is_correct',
        'answered_at',
    ];

    protected $casts = [
        'attempt_id' => 'integer',
        'pyq_question_id' => 'integer',
        'pyq_answer_id' => 'integer',
        'position' => 'integer',
        'is_correct' => 'boolean',
        'answered_at' => 'datetime',
    ];

    public function attempt()
    {
        return $this->belongsTo(PyqAttempt::class, 'attempt_id');
    }

    public function question()
    {
        return $this->belongsTo(PyqQuestion::class, 'pyq_question_id');
    }

    public function answer()
    {
        return $this->belongsTo(PyqAnswer::class, 'pyq_answer_id');
    }
}

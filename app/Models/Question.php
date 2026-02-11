<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'prompt',
        'explanation',
        'image_path',
        'difficulty',
        'subject_id',
        'topic_id',
        'language',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'difficulty' => 'integer',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class, 'quiz_question')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function answers()
    {
        return $this->hasMany(Answer::class)->orderBy('position');
    }
}

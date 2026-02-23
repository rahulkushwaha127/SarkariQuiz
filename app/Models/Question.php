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
        'subtopic_id',
        'language',
        'content_source_key',
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

    public function subtopic()
    {
        return $this->belongsTo(Subtopic::class);
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

    /**
     * Get translation siblings (same content_source_key, different languages).
     * Returns collection keyed by language code: ['en' => Question, 'hi' => Question, ...]
     */
    public function getTranslationSiblings(): \Illuminate\Support\Collection
    {
        if (! $this->content_source_key) {
            return collect();
        }

        return self::query()
            ->where('content_source_key', $this->content_source_key)
            ->where('id', '!=', $this->id)
            ->with('answers')
            ->get()
            ->keyBy('language');
    }

    /**
     * Get all questions in this translation group (including self), keyed by language.
     */
    public function getAllTranslations(): \Illuminate\Support\Collection
    {
        if (! $this->content_source_key) {
            return collect([$this->language => $this]);
        }

        return self::query()
            ->where('content_source_key', $this->content_source_key)
            ->with('answers')
            ->get()
            ->keyBy('language');
    }
}

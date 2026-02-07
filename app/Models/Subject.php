<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'name',
        'slug',
        'is_active',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Legacy single-exam relationship (nullable). Prefer exams() pivot.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * All exams this subject belongs to via the pivot.
     */
    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_subject')
            ->withPivot('position');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class)->orderBy('position')->orderBy('name');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
}


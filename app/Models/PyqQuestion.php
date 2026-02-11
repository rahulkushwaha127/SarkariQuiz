<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PyqQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'subject_id',
        'topic_id',
        'language',
        'year',
        'paper',
        'prompt',
        'explanation',
        'image_path',
        'position',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'exam_id' => 'integer',
        'subject_id' => 'integer',
        'topic_id' => 'integer',
        'year' => 'integer',
        'position' => 'integer',
    ];

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

    public function answers()
    {
        return $this->hasMany(PyqAnswer::class)->orderBy('position');
    }
}

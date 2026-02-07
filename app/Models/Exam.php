<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
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
     * Subjects linked to this exam via the exam_subject pivot.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'exam_subject')
            ->withPivot('position')
            ->orderByPivot('position')
            ->orderBy('subjects.name');
    }
}


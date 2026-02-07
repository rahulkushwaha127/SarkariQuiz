<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PyqAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'pyq_question_id',
        'title',
        'image_path',
        'is_correct',
        'position',
    ];

    protected $casts = [
        'pyq_question_id' => 'integer',
        'is_correct' => 'boolean',
        'position' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(PyqQuestion::class, 'pyq_question_id');
    }
}

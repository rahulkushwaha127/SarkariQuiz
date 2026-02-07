<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_id',
        'title',
        'image_path',
        'is_correct',
        'position',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'position' => 'integer',
    ];

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}

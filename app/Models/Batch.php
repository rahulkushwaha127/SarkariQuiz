<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Batch extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_user_id',
        'name',
        'description',
        'join_code',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $batch) {
            if (!$batch->join_code) {
                $batch->join_code = self::generateJoinCode();
            }
        });
    }

    public static function generateJoinCode(int $length = 6): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('join_code', $code)->exists());

        return $code;
    }

    /* ---- Relationships ---- */

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_user_id');
    }

    public function students()
    {
        return $this->hasMany(BatchStudent::class);
    }

    public function activeStudents()
    {
        return $this->students()->where('status', 'active');
    }

    public function quizzes()
    {
        return $this->hasMany(BatchQuiz::class);
    }

    /* ---- Scopes ---- */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }
}

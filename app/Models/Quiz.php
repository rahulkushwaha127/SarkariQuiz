<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'subject_id',
        'topic_id',
        'title',
        'description',
        'unique_code',
        'is_public',
        'is_featured',
        'featured_at',
        'difficulty',
        'language',
        'mode',
        'status',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'featured_at' => 'datetime',
        'difficulty' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $quiz) {
            if (! $quiz->unique_code) {
                $quiz->unique_code = self::generateUniqueCode();
            }
        });
    }

    public static function generateUniqueCode(int $length = 10): string
    {
        $length = max(6, min(12, $length));

        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('unique_code', $code)->exists());

        return $code;
    }

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

    public function questions()
    {
        return $this->hasMany(Question::class)->orderBy('position');
    }

    public function contests()
    {
        return $this->hasMany(Contest::class);
    }
}

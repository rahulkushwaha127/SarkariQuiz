<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'avatar_path',
        'social_links',
        'coaching_center_name',
        'coaching_city',
        'coaching_contact',
        'coaching_website',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

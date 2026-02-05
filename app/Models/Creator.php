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
        'headline',
        'tagline',
        'avatar_path',
        'cover_image_path',
        'gallery_images',
        'social_links',
        'coaching_center_name',
        'coaching_address',
        'coaching_city',
        'coaching_contact',
        'coaching_timings',
        'coaching_website',
        'courses_offered',
        'whatsapp_number',
    ];

    protected $casts = [
        'social_links' => 'array',
        'gallery_images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

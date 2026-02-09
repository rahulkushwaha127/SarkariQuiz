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
        'selected_students',
        'faculty',
        'section_visibility',
        'bio_theme',
    ];

    protected $casts = [
        'social_links'       => 'array',
        'gallery_images'     => 'array',
        'selected_students'  => 'array',
        'faculty'            => 'array',
        'section_visibility' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if a group or field is visible on the public page.
     * Keys: "about", "about.headline", "institute.address", etc.
     * Default is true (visible) when not explicitly set.
     */
    public function isVisible(string $key): bool
    {
        $vis = $this->section_visibility ?? [];
        return (bool) ($vis[$key] ?? true);
    }
}

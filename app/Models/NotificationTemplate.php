<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'channels',
        'email_subject',
        'email_body',
        'fcm_title',
        'fcm_body',
        'in_app_title',
        'in_app_body',
        'in_app_url',
        'available_variables',
        'is_active',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'channels'            => 'array',
            'available_variables' => 'array',
            'is_active'           => 'boolean',
            'is_system'           => 'boolean',
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

    /** Check whether a specific channel is enabled. */
    public function hasChannel(string $channel): bool
    {
        return in_array($channel, $this->channels ?? [], true);
    }

    /** Render a template string, replacing {{var}} placeholders. */
    public static function render(?string $template, array $variables): string
    {
        if ($template === null || $template === '') {
            return '';
        }

        return preg_replace_callback('/\{\{(\w+)\}\}/', function ($matches) use ($variables) {
            return $variables[$matches[1]] ?? '';
        }, $template);
    }

    /* ------------------------------------------------------------------ */
    /*  Scopes                                                             */
    /* ------------------------------------------------------------------ */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }
}

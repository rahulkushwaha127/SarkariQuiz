<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    public const CACHE_PREFIX = 'setting:';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = self::query()->where('key', $key)->first();
        if (! $row) {
            return $default;
        }
        return $row->value;
    }

    public static function set(string $key, mixed $value): void
    {
        self::query()->updateOrCreate(
            ['key' => $key],
            ['value' => is_scalar($value) || $value === null ? (string) $value : json_encode($value)]
        );
    }

    public static function cachedGet(string $key, mixed $default = null, int $ttlSeconds = 300): mixed
    {
        return Cache::remember(self::CACHE_PREFIX . $key, $ttlSeconds, fn () => self::get($key, $default));
    }

    public static function forget(string $key): void
    {
        Cache::forget(self::CACHE_PREFIX . $key);
    }
}


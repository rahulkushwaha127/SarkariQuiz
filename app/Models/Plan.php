<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'name', 'interval', 'currency', 'unit_amount', 'trial_days', 'users_count', 'teams_count', 'roles_count', 'is_active', 'features', 'provider_prices', 'description'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'provider_prices' => 'array',
        'unit_amount' => 'integer',
        'trial_days' => 'integer',
        'users_count' => 'integer',
        'teams_count' => 'integer',
        'roles_count' => 'integer',
    ];

    public function getProviderPrice(string $provider): ?string
    {
        $map = $this->provider_prices ?? [];
        return is_array($map) ? ($map[$provider] ?? null) : null;
    }
}



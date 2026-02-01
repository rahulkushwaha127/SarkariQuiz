<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'provider',
        'code_html',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assignments()
    {
        return $this->hasMany(AdSlotAssignment::class, 'ad_unit_id');
    }
}


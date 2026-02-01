<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'context',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assignment()
    {
        return $this->hasOne(AdSlotAssignment::class, 'slot_id');
    }
}


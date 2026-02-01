<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdSlotAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'slot_id',
        'ad_unit_id',
        'enabled',
        'rules_json',
    ];

    protected $casts = [
        'enabled' => 'boolean',
        'rules_json' => 'array',
    ];

    public function slot()
    {
        return $this->belongsTo(AdSlot::class, 'slot_id');
    }

    public function adUnit()
    {
        return $this->belongsTo(AdUnit::class, 'ad_unit_id');
    }
}


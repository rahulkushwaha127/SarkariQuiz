<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestWhitelist extends Model
{
    use HasFactory;

    protected $table = 'contest_whitelist';

    protected $fillable = [
        'contest_id',
        'email',
        'phone',
    ];

    public function contest()
    {
        return $this->belongsTo(Contest::class);
    }
}


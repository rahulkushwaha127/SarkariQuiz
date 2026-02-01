<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PyqImportLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'filename',
        'rows_total',
        'rows_created',
        'rows_skipped',
        'rows_failed',
        'meta_json',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'rows_total' => 'integer',
        'rows_created' => 'integer',
        'rows_skipped' => 'integer',
        'rows_failed' => 'integer',
        'meta_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

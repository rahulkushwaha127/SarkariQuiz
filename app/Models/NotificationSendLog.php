<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationSendLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'unique_key',
        'payload',
        'recipient_count',
        'sent_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'recipient_count' => 'integer',
        'sent_at' => 'datetime',
    ];
}


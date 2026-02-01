<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClubSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id',
        'status',
        'started_at',
        'ended_at',
        'created_by_user_id',
        'ended_by_user_id',
        'current_master_user_id',
        'current_master_position',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'current_master_position' => 'integer',
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function endedBy()
    {
        return $this->belongsTo(User::class, 'ended_by_user_id');
    }

    public function currentMaster()
    {
        return $this->belongsTo(User::class, 'current_master_user_id');
    }

    public function turns()
    {
        return $this->hasMany(ClubSessionTurn::class, 'session_id')->orderBy('position');
    }

    public function scores()
    {
        return $this->hasMany(ClubSessionScore::class, 'session_id');
    }
}


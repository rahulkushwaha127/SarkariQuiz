<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_user_id',
        'status',
        'invite_token',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $club) {
            if (!$club->invite_token) {
                $club->invite_token = Str::random(40);
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function members()
    {
        return $this->hasMany(ClubMember::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(ClubJoinRequest::class);
    }

    public function sessions()
    {
        return $this->hasMany(ClubSession::class);
    }

    public function activeSession()
    {
        return $this->hasOne(ClubSession::class)->where('status', 'active');
    }
}


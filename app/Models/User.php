<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'bio',
        'avatar_path',
        'social_links',
        'coaching_center_name',
        'coaching_city',
        'coaching_contact',
        'coaching_website',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'social_links' => 'array',
            'blocked_at' => 'datetime',
        ];
    }

    public function studentProfile()
    {
        return $this->hasOne(Student::class);
    }

    public function creatorProfile()
    {
        return $this->hasOne(Creator::class);
    }

    public function adminProfile()
    {
        return $this->hasOne(Admin::class);
    }

    public function fcmTokens()
    {
        return $this->hasMany(FcmToken::class);
    }

    public function contestsCreated()
    {
        return $this->hasMany(Contest::class, 'creator_user_id');
    }
}

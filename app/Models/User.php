<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Lab404\Impersonate\Models\Impersonate;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, Impersonate;

    /**
     * Only super_admin users can impersonate others.
     */
    public function canImpersonate(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Super admins cannot be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        return ! $this->hasRole('super_admin');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'google_id',
        'google_avatar_url',
        'username',
        'password',
        'bio',
        'avatar_path',
        'social_links',
        'coaching_center_name',
        'coaching_city',
        'coaching_contact',
        'coaching_website',
        'is_guest',
        'openai_api_key',
        'openai_model',
        'gemini_api_key',
        'anthropic_api_key',
        'default_ai_provider',
        'plan_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'openai_api_key',
        'gemini_api_key',
        'anthropic_api_key',
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
            'is_guest' => 'boolean',
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

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function contestsCreated()
    {
        return $this->hasMany(Contest::class, 'creator_user_id');
    }

    public function batchesCreated()
    {
        return $this->hasMany(Batch::class, 'creator_user_id');
    }

    public function batchMemberships()
    {
        return $this->hasMany(BatchStudent::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Plan                                                               */
    /* ------------------------------------------------------------------ */

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Return the user's assigned plan, or the system default plan.
     */
    public function activePlan(): ?Plan
    {
        return $this->plan ?? Plan::defaultPlan();
    }
}

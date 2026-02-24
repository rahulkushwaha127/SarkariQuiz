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
        'student_plan_id',
        'referred_by_id',
        'referral_code',
        'student_plan_ends_at',
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
            'student_plan_ends_at' => 'datetime',
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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /* ------------------------------------------------------------------ */
    /*  Creator plan (limits: quizzes, batches, AI, etc.)                   */
    /* ------------------------------------------------------------------ */

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Creator's effective plan, or the system default creator plan.
     */
    public function activePlan(): ?Plan
    {
        return $this->plan ?? Plan::defaultPlan();
    }

    /* ------------------------------------------------------------------ */
    /*  Student plan (subscription tier: Free, Premium, etc.)              */
    /* ------------------------------------------------------------------ */

    public function studentPlan()
    {
        return $this->belongsTo(StudentPlan::class, 'student_plan_id');
    }

    /**
     * Student's current subscription plan (what they bought or were assigned).
     * Returns null if plan has expired (student_plan_ends_at in the past).
     */
    public function activeStudentPlan(): ?StudentPlan
    {
        if (! $this->student_plan_id) {
            return null;
        }
        if ($this->student_plan_ends_at && $this->student_plan_ends_at->isPast()) {
            return null;
        }
        return $this->studentPlan;
    }

    /**
     * Whether the user has an active student plan (not expired).
     */
    public function hasActiveStudentPlan(): bool
    {
        return $this->activeStudentPlan() !== null;
    }

    /* ------------------------------------------------------------------ */
    /*  Referral                                                           */
    /* ------------------------------------------------------------------ */

    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    /** The one referral reward this user has received (if any). One reward per user. */
    public function referralRewardReceived()
    {
        return $this->hasOne(ReferralReward::class, 'referrer_id');
    }

    /**
     * Ensure this user has a unique referral_code (generates and saves if missing).
     */
    public function ensureReferralCode(): string
    {
        if ($this->referral_code !== null && $this->referral_code !== '') {
            return $this->referral_code;
        }
        $code = strtoupper(\Illuminate\Support\Str::random(8));
        while (self::query()->where('referral_code', $code)->exists()) {
            $code = strtoupper(\Illuminate\Support\Str::random(8));
        }
        $this->update(['referral_code' => $code]);
        return $code;
    }

    /**
     * Full referral link for this user (generates referral_code if needed).
     */
    public function getReferralLink(): string
    {
        $code = $this->ensureReferralCode();
        return route('register', ['ref' => $code]);
    }

    /**
     * Preferred content language for filtering quizzes/questions (students).
     * Returns student profile preferred_language or app locale for guests.
     */
    public function preferredContentLanguage(): string
    {
        $lang = $this->studentProfile?->preferred_language ?? null;

        return is_string($lang) && $lang !== '' ? $lang : config('app.locale', 'en');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
        'logo',
        'website',
        'is_active',
        'settings',
        'billing_accounts',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'billing_accounts' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($team) {
            if (empty($team->slug)) {
                $team->slug = Str::slug($team->name);
            }
        });
    }

    /**
     * Get the owner of the team.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all users that belong to the team.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    /**
     * Check if a user is a member of this team.
     */
    public function hasMember(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if a user is the owner or admin.
     */
    public function isOwnerOrAdmin(User $user): bool
    {
        if ($this->user_id === $user->id) {
            return true;
        }

        $member = $this->users()->where('user_id', $user->id)->first();
        
        return $member && in_array($member->pivot->role, ['owner', 'admin']);
    }
}

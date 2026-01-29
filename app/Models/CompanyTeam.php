<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class CompanyTeam extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id', // company id (teams table)
        'name',
        'slug',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'company_team_user');
    }
}



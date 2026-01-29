<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->email_verified_at,
            'is_super_admin' => $this->is_super_admin,
            'current_team' => $this->when($this->relationLoaded('currentTeam'), function () {
                return $this->currentTeam() ? [
                    'id' => $this->currentTeam()->id,
                    'name' => $this->currentTeam()->name,
                    'slug' => $this->currentTeam()->slug,
                ] : null;
            }),
            'roles' => $this->when($this->relationLoaded('roles'), function () {
                return $this->getRoleNames();
            }),
            'permissions' => $this->when($this->relationLoaded('permissions'), function () {
                return $this->getPermissionNames();
            }),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


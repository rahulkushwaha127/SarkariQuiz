<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            'interval' => $this->interval,
            'currency' => $this->currency,
            'unit_amount' => $this->unit_amount,
            'formatted_amount' => number_format($this->unit_amount / 100, 2) . ' ' . strtoupper($this->currency),
            'trial_days' => $this->trial_days,
            'users_count' => $this->users_count,
            'teams_count' => $this->teams_count,
            'roles_count' => $this->roles_count,
            'features' => $this->features,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


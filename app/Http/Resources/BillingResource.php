<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillingResource extends JsonResource
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
            'kind' => $this->kind,
            'plan_code' => $this->plan_code,
            'status' => $this->status,
            'type' => $this->type,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'formatted_amount' => $this->amount ? number_format($this->amount / 100, 2) . ' ' . strtoupper($this->currency) : null,
            'tx_status' => $this->tx_status,
            'invoice_number' => $this->invoice_number,
            'provider' => $this->provider,
            'occurred_at' => $this->occurred_at,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}


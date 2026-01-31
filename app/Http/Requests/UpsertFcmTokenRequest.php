<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertFcmTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'min:20'],
            'platform' => ['nullable', 'string', 'max:30'],
            'device_id' => ['nullable', 'string', 'max:100'],
        ];
    }
}


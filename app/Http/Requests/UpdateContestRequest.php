<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateContestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'quiz_id' => ['nullable', 'integer', Rule::exists('quizzes', 'id')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'join_mode' => ['required', 'in:public,link,code,whitelist'],
            'is_public_listed' => ['sometimes', 'boolean'],
            'status' => ['required', 'in:draft,scheduled,live,ended,cancelled'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}


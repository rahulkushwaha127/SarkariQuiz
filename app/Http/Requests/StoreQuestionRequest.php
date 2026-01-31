<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'prompt' => ['required', 'string'],
            'explanation' => ['nullable', 'string'],
            'answers' => ['required', 'array', 'size:4'],
            'answers.*.title' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'min:0', 'max:3'],
        ];
    }
}

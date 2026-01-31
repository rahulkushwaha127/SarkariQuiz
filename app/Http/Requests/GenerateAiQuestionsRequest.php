<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAiQuestionsRequest extends FormRequest
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
            'input_type' => ['required', 'in:text,url,pdf,docx,image'],
            'text' => ['nullable', 'string'],
            'url' => ['nullable', 'url'],
            'file' => ['nullable', 'file', 'max:10240'],
            'num_questions' => ['required', 'integer', 'min:1', 'max:25'],
            'replace_existing' => ['sometimes', 'boolean'],
        ];
    }
}

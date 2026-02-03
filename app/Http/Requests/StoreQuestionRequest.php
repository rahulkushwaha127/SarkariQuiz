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
            'language' => ['nullable', 'string', 'max:10', 'in:'.implode(',', array_keys(config('question.languages', ['en' => 'English', 'hi' => 'Hindi'])))],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'topic_id' => [
                'nullable',
                'integer',
                'exists:topics,id',
                \Illuminate\Validation\Rule::exists('topics', 'id')->where('subject_id', $this->input('subject_id')),
            ],
            'answers' => ['required', 'array', 'size:4'],
            'answers.*.title' => ['required', 'string', 'max:255'],
            'correct_index' => ['required', 'integer', 'min:0', 'max:3'],
        ];
    }
}

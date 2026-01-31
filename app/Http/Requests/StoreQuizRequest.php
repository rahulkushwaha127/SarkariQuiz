<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuizRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_public' => ['sometimes', 'boolean'],
            'difficulty' => ['required', 'integer', 'min:0', 'max:2'],
            'language' => ['required', 'string', 'max:10'],
            'mode' => ['required', 'in:exam,study'],
            'status' => ['required', 'in:draft,pending,approved,rejected,published'],

            'exam_id' => ['nullable', 'integer', Rule::exists('exams', 'id')],
            'subject_id' => [
                'nullable',
                'integer',
                Rule::exists('subjects', 'id')->when($this->input('exam_id'), fn ($q) => $q->where('exam_id', $this->input('exam_id'))),
            ],
            'topic_id' => [
                'nullable',
                'integer',
                Rule::exists('topics', 'id')->when($this->input('subject_id'), fn ($q) => $q->where('subject_id', $this->input('subject_id'))),
            ],
        ];
    }
}

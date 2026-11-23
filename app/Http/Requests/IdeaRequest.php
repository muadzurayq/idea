<?php

namespace App\Http\Requests;

use App\IdeaStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class IdeaRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['nullable', new Enum(ideaStatus::class)],
            'image' => ['nullable', 'image', 'max:2048'], // بحد أقصى 2MB
            'links' => ['nullable', 'array'],
            'links.*' => ['nullable', 'url', 'active_url'],
            'steps' => ['nullable', 'array'],
            'steps.*.description' => ['nullable', 'string', 'max:255'],
            'steps.*.completed' => ['boolean'],
        ];
    }
}

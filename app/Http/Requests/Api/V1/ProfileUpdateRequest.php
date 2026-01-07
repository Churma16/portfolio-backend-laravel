<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'avatar' => ['nullable', 'file:png,jpg,jpeg', 'max:2000'],
            'name' => ['required', 'string', 'max:200'],
            'headline' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'bio_short' => ['nullable', 'string'],
            'bio_long' => ['nullable', 'string'],
            'cv_files' => ['nullable', 'file:pdf', 'max:2000'],
            'is_hierable' => ['nullable', 'boolean'],
            'socials' => ['nullable', 'json'],
        ];
    }
}

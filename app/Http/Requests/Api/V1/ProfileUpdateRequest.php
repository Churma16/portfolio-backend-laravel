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
            'avatar' => ['nullable', 'file', 'mimes:png,jpg,jpeg', 'max:5120'],
            'name' => ['required', 'string', 'max:200'],
            'headline' => ['nullable', 'string', 'max:255'],
            'role' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'bio_short' => ['nullable', 'string'],
            'bio_long' => ['nullable', 'string'],
            'cv_files' => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'is_hireable' => ['nullable', 'boolean'],
            'socials' => ['nullable', 'json'],
            'hero_image_codes' => ['nullable', 'json'],
        ];
    }
}

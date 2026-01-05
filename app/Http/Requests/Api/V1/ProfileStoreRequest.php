<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProfileStoreRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:200'],
            'headline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string', 'max:500'],
            'cv_files' => ['nullable', 'string', 'max:500'],
            'socials' => ['nullable', 'json'],
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:200', 'unique:projects,slug'],
            'thumbnail' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'demo_url' => ['nullable', 'string', 'max:500'],
            'repo_url' => ['nullable', 'string', 'max:500'],
            'is_featured' => ['required'],
            'published_at' => ['nullable'],
        ];
    }
}

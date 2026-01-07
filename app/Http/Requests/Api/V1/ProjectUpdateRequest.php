<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
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
            // 'slug' => ['required', 'string', 'max:200', 'unique:projects,slug'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:10000'],
            'content' => ['nullable', 'string'],
            'demo_url' => ['nullable', 'string', 'max:500'],
            'repo_url' => ['nullable', 'string', 'max:500'],
            // 'is_featured' => ['required'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'published_at' => ['nullable'],
        ];
    }
}

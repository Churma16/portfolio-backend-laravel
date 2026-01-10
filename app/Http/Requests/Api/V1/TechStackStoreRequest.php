<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class TechStackStoreRequest extends FormRequest
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
        $this->merge(['slug' => Str::slug($this->name)]);

        return [
            'name' => ['required', 'string', 'max:100'],
             'slug' => ['required', 'string', 'max:100', 'unique:tech_stacks,slug'],
            'icon' => ['nullable', 'string', 'max:255'],
            'category_id' => ['nullable', 'integer', 'exists:Categories,id'],
        ];
    }
}

<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class TechStackCategoryStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->name) {
            $this->merge(['slug' => \Illuminate\Support\Str::slug($this->name)]);
        }

        return [
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'unique:tech_stack_categories,slug'],
            'color' => ['nullable', 'string', 'max:50'],
        ];
    }
}

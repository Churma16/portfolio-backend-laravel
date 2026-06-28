<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class TechStackCategoryUpdateRequest extends FormRequest
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

        $id = $this->route('tech_stack_category')->id ?? $this->route('tech_stack_category');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => ['sometimes', 'required', 'string', 'max:100', 'unique:tech_stack_categories,slug,' . $id],
            'color' => ['nullable', 'string', 'max:50'],
        ];
    }
}

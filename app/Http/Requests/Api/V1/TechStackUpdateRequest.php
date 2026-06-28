<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class TechStackUpdateRequest extends FormRequest
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
        if ($this->name) {
            $this->merge(['slug' => Str::slug($this->name)]);
        }

        $id = $this->route('tech_stack')->id ?? $this->route('tech_stack');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'slug' => ['sometimes', 'required', 'string', 'max:100', 'unique:tech_stacks,slug,' . $id],
            'icon' => ['nullable', 'string', 'max:255'],
            'tech_stack_category_id' => ['nullable', 'integer', 'exists:tech_stack_categories,id'],
        ];
    }
}

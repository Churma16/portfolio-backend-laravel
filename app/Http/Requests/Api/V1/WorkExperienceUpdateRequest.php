<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class WorkExperienceUpdateRequest extends FormRequest
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
            'company' => ['required', 'string', 'max:200'],
            'position' => ['required', 'string', 'max:200'],
            'location' => ['nullable', 'string', 'max:200'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date'],
            'is_current' => ['required'],
            'description' => ['nullable', 'string'],
        ];
    }
}

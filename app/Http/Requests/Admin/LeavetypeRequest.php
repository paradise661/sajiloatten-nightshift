<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LeavetypeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'duration' => 'required|integer',
            'requires_advance_application' => 'nullable|boolean',
            'min_days_before' => 'required_if:requires_advance_application,1|nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'min_days_before.required_if' => 'The minimum days before field is required when advance application is enabled.',
        ];
    }
}

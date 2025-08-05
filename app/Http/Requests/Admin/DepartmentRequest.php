<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DepartmentRequest extends FormRequest
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
            'name' => 'required',
            'branch_id' => 'required',
            'shift' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'branch_id.required' => 'Please Select Branch.',
            'shift.required' => 'Please Select Shift.'
        ];
    }
}

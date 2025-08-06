<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShiftRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
        ];

        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        foreach ($days as $day) {
            // Validate start_time and end_time only if not holiday
            $rules["{$day}_start_time"] = 'nullable|date_format:H:i';
            $rules["{$day}_end_time"] = 'nullable|date_format:H:i';
            $rules["{$day}_holiday"] = 'nullable';
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            $isCrossDay = $this->input('is_cross_day'); // Check if checkbox is checked

            foreach ($days as $day) {
                if (!$this->has("{$day}_holiday")) {
                    $start = $this->input("{$day}_start_time");
                    $end = $this->input("{$day}_end_time");

                    if ($start && $end) {
                        $startTimestamp = strtotime($start);
                        $endTimestamp = strtotime($end);

                        if (!$isCrossDay && $endTimestamp <= $startTimestamp) {
                            $validator->errors()->add("{$day}_end_time", ucfirst($day) . " End Time must be greater than Start Time.");
                        }
                    } else {
                        $validator->errors()->add("{$day}_start_time", ucfirst($day) . " Start Time is required unless it's a holiday.");
                        $validator->errors()->add("{$day}_end_time", ucfirst($day) . " End Time is required unless it's a holiday.");
                    }
                }
            }
        });
    }
}

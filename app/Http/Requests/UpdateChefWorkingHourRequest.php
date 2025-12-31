<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChefWorkingHourRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sanctum + chef role handled by route
    }

    public function rules(): array
    {
        return [
            'day_of_week' => ['sometimes', 'integer', 'min:0', 'max:6'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $start = $this->input('start_time');
            $end = $this->input('end_time');
            if ($start && $end && $start >= $end) {
                $validator->errors()->add('end_time', 'وقت النهاية يجب أن يكون بعد وقت البداية');
            }
        });
    }
}

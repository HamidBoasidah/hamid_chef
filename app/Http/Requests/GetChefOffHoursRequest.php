<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetChefOffHoursRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'day_of_week' => ['nullable', 'integer', 'min:0', 'max:6'],
        ];
    }
}

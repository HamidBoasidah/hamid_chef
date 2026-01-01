<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ListChefVacationsByMonthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Optional month filter in format YYYY-MM; defaults to current month if not provided
            'month' => ['nullable', 'date_format:Y-m'],
        ];
    }
}

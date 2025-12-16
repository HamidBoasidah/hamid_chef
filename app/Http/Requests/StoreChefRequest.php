<?php

namespace App\Http\Requests;

use App\Rules\UniqueChefForUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\ValidationException as AppValidationException;

class StoreChefRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // user_id is set server-side for API requests; do not require it from the client
            // Add validation to ensure user doesn't already have a chef profile
            'user_id' => [new UniqueChefForUser()],
            'name' => 'required|string|max:255',
            'short_description' => 'nullable|string|max:255',
            'long_description' => 'nullable|string|max:2000',
            'email' => 'nullable|email|unique:chefs,email',
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => 'nullable|string|max:1000',
            'base_hourly_rate' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'governorate_id' => 'required|exists:governorates,id',
            'district_id' => 'required|exists:districts,id',
            'area_id' => 'required|exists:areas,id',
            'logo' => 'nullable|image|max:4096',
            'banner' => 'nullable|image|max:4096',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }

    /**
     * Convert failed validation into our application ValidationException so API
     * responses keep a consistent JSON shape.
     */
    protected function failedValidation(Validator $validator)
    {
        throw AppValidationException::withMessages($validator->errors()->toArray());
    }
}

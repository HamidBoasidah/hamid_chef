<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\ValidationException as AppValidationException;

class UpdateChefRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // For API update we accept partial payloads (sometimes) and do not require client to send user_id
        $chefId = optional($this->route('chef'))->id ?? $this->route('chef');

        return [
            'name' => 'sometimes|required|string|max:255',
            'short_description' => 'sometimes|nullable|string|max:255',
            'long_description' => 'sometimes|nullable|string|max:2000',
            'email' => 'sometimes|nullable|email|unique:chefs,email,' . $chefId,
            'phone' => ['sometimes', 'nullable', 'string', 'max:50'],
            'address' => 'sometimes|nullable|string|max:1000',
            'base_hourly_rate' => 'sometimes|nullable|numeric|min:0',
            'is_active' => 'sometimes|nullable|boolean',
            'governorate_id' => 'sometimes|nullable|exists:governorates,id',
            'district_id' => 'sometimes|nullable|exists:districts,id',
            'area_id' => 'sometimes|nullable|exists:areas,id',
            'logo' => 'sometimes|nullable|image|max:4096',
            'banner' => 'sometimes|nullable|image|max:4096',
            'created_by' => 'sometimes|nullable|exists:users,id',
            'updated_by' => 'sometimes|nullable|exists:users,id',
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

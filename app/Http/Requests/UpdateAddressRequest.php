<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\ValidationException as AppValidationException;

class UpdateAddressRequest extends FormRequest
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
            'label' => 'sometimes|nullable|string|max:50',
            'address' => 'sometimes|required|string|max:1000',
            'governorate_id' => 'sometimes|nullable|exists:governorates,id',
            'district_id' => 'sometimes|nullable|exists:districts,id',
            'area_id' => 'sometimes|nullable|exists:areas,id',
            'street' => 'sometimes|nullable|string|max:255',
            'building_number' => 'sometimes|nullable|integer|min:0',
            'floor_number' => 'sometimes|nullable|integer|min:0',
            'apartment_number' => 'sometimes|nullable|integer|min:0',
            'is_default' => 'sometimes|nullable|boolean',
            'is_active' => 'sometimes|nullable|boolean',
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

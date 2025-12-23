<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\ValidationException as AppValidationException;

class StoreChefServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chef_id' => 'required|exists:chefs,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'service_type' => 'required|in:hourly,package',
            'hourly_rate' => 'nullable|numeric|min:0',
            'min_hours' => 'nullable|integer|min:1',
            'package_price' => 'nullable|numeric|min:0',
            'max_guests_included' => 'nullable|integer|min:1',
            'allow_extra_guests' => 'nullable|boolean',
            'extra_guest_price' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
            'feature_image' => 'nullable|image|max:5120', // 5MB
            'service_images' => 'nullable|array|max:10',
            'service_images.*' => 'image|max:5120', // 5MB per image
            'equipment' => 'nullable|array',
            'equipment.*.name' => 'required_with:equipment.*|string|max:100',
            'equipment.*.is_included' => 'nullable|boolean',
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
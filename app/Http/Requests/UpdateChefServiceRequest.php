<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\ValidationException as AppValidationException;

class UpdateChefServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string|max:2000',
            'service_type' => 'sometimes|required|in:hourly,package',
            'hourly_rate' => 'sometimes|nullable|numeric|min:0',
            'min_hours' => 'sometimes|nullable|integer|min:1',
            'package_price' => 'sometimes|nullable|numeric|min:0',
            'max_guests_included' => 'sometimes|nullable|integer|min:1',
            'allow_extra_guests' => 'sometimes|nullable|boolean',
            'extra_guest_price' => 'sometimes|nullable|numeric|min:0',
            'is_active' => 'sometimes|nullable|boolean',
            'tags' => 'sometimes|nullable|array',
            'tags.*' => 'exists:tags,id',
            'feature_image' => 'sometimes|nullable|image|max:5120', // 5MB
            'service_images' => 'sometimes|nullable|array|max:10',
            'service_images.*' => 'image|max:5120', // 5MB per image
            'delete_service_image_ids' => 'sometimes|nullable|array',
            'delete_service_image_ids.*' => 'integer|exists:chef_service_images,id',
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
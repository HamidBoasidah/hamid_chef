<?php

namespace App\Http\Requests;

use App\Exceptions\AppValidationException;
use App\Models\ChefService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreChefServiceEquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $service = ChefService::find($this->chef_service_id);
        
        if (!$service) {
            return false;
        }

        // Check if user can update the service (which includes managing equipment)
        return $this->user()->can('update', $service);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'chef_service_id' => 'required|exists:chef_services,id',
            'name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                // Custom rule to check uniqueness within the service
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\ChefServiceEquipment::where('chef_service_id', $this->chef_service_id)
                                                             ->where('name', $value)
                                                             ->exists();
                    if ($exists) {
                        $fail('Equipment with this name already exists for this service.');
                    }
                },
            ],
            'is_included' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'chef_service_id.required' => 'Service ID is required.',
            'chef_service_id.exists' => 'The selected service does not exist.',
            'name.required' => 'Equipment name is required.',
            'name.string' => 'Equipment name must be a string.',
            'name.min' => 'Equipment name must be at least 2 characters.',
            'name.max' => 'Equipment name must not exceed 100 characters.',
            'is_included.required' => 'You must specify whether the equipment is included in the service.',
            'is_included.boolean' => 'The included field must be true or false.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'chef_service_id' => 'service',
            'name' => 'equipment name',
            'is_included' => 'inclusion status',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new AppValidationException($validator);
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Trim whitespace from name
        if ($this->has('name')) {
            $this->merge(['name' => trim($this->name)]);
        }
    }

    /**
     * Get the validated data from the request with additional processing.
     */
    public function validatedWithDefaults(): array
    {
        return $this->validated();
    }
}
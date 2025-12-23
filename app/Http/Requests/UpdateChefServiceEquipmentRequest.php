<?php

namespace App\Http\Requests;

use App\Exceptions\AppValidationException;
use App\Models\ChefServiceEquipment;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateChefServiceEquipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $equipment = ChefServiceEquipment::find($this->route('id'));
        
        if (!$equipment) {
            return false;
        }

        // Check if user can update the service (which includes managing equipment)
        return $this->user()->can('update', $equipment->chefService);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $equipmentId = $this->route('id');
        $equipment = ChefServiceEquipment::find($equipmentId);
        
        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'min:2',
                'max:100',
                // Custom rule to check uniqueness within the service (excluding current equipment)
                function ($attribute, $value, $fail) use ($equipment) {
                    if ($equipment) {
                        $exists = \App\Models\ChefServiceEquipment::where('chef_service_id', $equipment->chef_service_id)
                                                                 ->where('name', $value)
                                                                 ->where('id', '!=', $equipment->id)
                                                                 ->exists();
                        if ($exists) {
                            $fail('Equipment with this name already exists for this service.');
                        }
                    }
                },
            ],
            'is_included' => 'sometimes|required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
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
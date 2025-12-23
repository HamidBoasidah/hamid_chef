<?php

namespace App\Http\Requests;

use App\Exceptions\AppValidationException;
use App\Models\ChefService;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BulkManageEquipmentRequest extends FormRequest
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
            'equipment' => 'required|array|min:1',
            'equipment.*.id' => 'nullable|exists:chef_service_equipment,id',
            'equipment.*.name' => [
                'required',
                'string',
                'min:2',
                'max:100',
            ],
            'equipment.*.description' => 'nullable|string|max:500',
            'equipment.*.is_included' => 'required|boolean',
            'equipment.*.is_active' => 'sometimes|boolean',
            'delete_ids' => 'sometimes|array',
            'delete_ids.*' => 'exists:chef_service_equipment,id',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateEquipmentNames($validator);
            $this->validateDeleteIds($validator);
        });
    }

    /**
     * Validate that equipment names are unique within the service.
     */
    protected function validateEquipmentNames($validator)
    {
        $equipment = $this->input('equipment', []);
        $names = [];
        
        foreach ($equipment as $index => $item) {
            $name = trim($item['name'] ?? '');
            
            if (empty($name)) {
                continue;
            }
            
            // Check for duplicates within the request
            if (in_array($name, $names)) {
                $validator->errors()->add(
                    "equipment.{$index}.name",
                    'Duplicate equipment names are not allowed.'
                );
            } else {
                $names[] = $name;
            }
            
            // Check for duplicates in database (excluding current item if updating)
            $query = \App\Models\ChefServiceEquipment::where('chef_service_id', $this->chef_service_id)
                                                   ->where('name', $name);
            
            if (isset($item['id'])) {
                $query->where('id', '!=', $item['id']);
            }
            
            if ($query->exists()) {
                $validator->errors()->add(
                    "equipment.{$index}.name",
                    'Equipment with this name already exists for this service.'
                );
            }
        }
    }

    /**
     * Validate that delete IDs belong to the specified service.
     */
    protected function validateDeleteIds($validator)
    {
        $deleteIds = $this->input('delete_ids', []);
        
        if (empty($deleteIds)) {
            return;
        }
        
        $validIds = \App\Models\ChefServiceEquipment::where('chef_service_id', $this->chef_service_id)
                                                   ->whereIn('id', $deleteIds)
                                                   ->pluck('id')
                                                   ->toArray();
        
        $invalidIds = array_diff($deleteIds, $validIds);
        
        if (!empty($invalidIds)) {
            $validator->errors()->add(
                'delete_ids',
                'Some equipment IDs do not belong to this service: ' . implode(', ', $invalidIds)
            );
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'chef_service_id.required' => 'Service ID is required.',
            'chef_service_id.exists' => 'The selected service does not exist.',
            'equipment.required' => 'Equipment data is required.',
            'equipment.array' => 'Equipment must be an array.',
            'equipment.min' => 'At least one equipment item is required.',
            'equipment.*.id.exists' => 'The selected equipment does not exist.',
            'equipment.*.name.required' => 'Equipment name is required.',
            'equipment.*.name.string' => 'Equipment name must be a string.',
            'equipment.*.name.min' => 'Equipment name must be at least 2 characters.',
            'equipment.*.name.max' => 'Equipment name must not exceed 100 characters.',
            'equipment.*.description.string' => 'Description must be a string.',
            'equipment.*.description.max' => 'Description must not exceed 500 characters.',
            'equipment.*.is_included.required' => 'You must specify whether the equipment is included.',
            'equipment.*.is_included.boolean' => 'The included field must be true or false.',
            'equipment.*.is_active.boolean' => 'The active field must be true or false.',
            'delete_ids.array' => 'Delete IDs must be an array.',
            'delete_ids.*.exists' => 'The selected equipment does not exist.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'chef_service_id' => 'service',
            'equipment' => 'equipment',
            'delete_ids' => 'equipment to delete',
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
        // Trim whitespace from equipment names and descriptions
        $equipment = $this->input('equipment', []);
        
        foreach ($equipment as $index => $item) {
            if (isset($item['name'])) {
                $equipment[$index]['name'] = trim($item['name']);
            }
            
            if (isset($item['description']) && $item['description'] !== null) {
                $equipment[$index]['description'] = trim($item['description']);
            }
            
            // Set default value for is_active if not provided
            if (!isset($item['is_active'])) {
                $equipment[$index]['is_active'] = true;
            }
        }
        
        $this->merge(['equipment' => $equipment]);
    }

    /**
     * Get the validated data from the request with additional processing.
     */
    public function validatedWithDefaults(): array
    {
        $validated = $this->validated();
        
        // Add audit fields to equipment
        foreach ($validated['equipment'] as $index => $item) {
            if (isset($item['id'])) {
                // Updating existing equipment
                $validated['equipment'][$index]['updated_by'] = auth()->id();
            } else {
                // Creating new equipment
                $validated['equipment'][$index]['created_by'] = auth()->id();
                $validated['equipment'][$index]['chef_service_id'] = $validated['chef_service_id'];
            }
        }
        
        return $validated;
    }
}
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChefRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id|unique:chefs,user_id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:chefs,email',
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => 'nullable|string|max:1000',
            'base_hourly_rate' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'governorate_id' => 'nullable|exists:governorates,id',
            'district_id' => 'nullable|exists:districts,id',
            'area_id' => 'nullable|exists:areas,id',
            'logo' => 'nullable|image|max:4096',
            'banner' => 'nullable|image|max:4096',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }
}

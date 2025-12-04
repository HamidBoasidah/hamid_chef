<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChefRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $chef = $this->route('chef');
        $chefId = $chef?->id ?? null;

        return [
            'user_id' => $chefId
                ? [
                    'required',
                    'exists:users,id',
                    Rule::unique('chefs', 'user_id')->ignore($chefId),
                ]
                : 'required|exists:users,id|unique:chefs,user_id',
            'name' => 'required|string|max:255',
            'email' => $chefId
                ? "nullable|email|unique:chefs,email,{$chefId}"
                : 'nullable|email|unique:chefs,email',
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => 'nullable|string|max:1000',
            'base_hourly_rate' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
            'governorate_id' => 'nullable|exists:governorates,id',
            'district_id' => 'nullable|exists:districts,id',
            'area_id' => 'nullable|exists:areas,id',
            'logo' => 'nullable|file|image|max:4096',
            'banner' => 'nullable|file|image|max:4096',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }
}

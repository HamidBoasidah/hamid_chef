<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
        $userId = optional($this->route('user'))->id ?? $this->route('user');
        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $userId,
            'address' => 'nullable|string|max:255',
            'phone_number' => ['nullable', 'regex:/^\d{9}$/'],
            'whatsapp_number' => ['nullable', 'regex:/^\d{9}$/'],
            'is_active' => 'boolean',
            'password' => 'nullable|string|min:8',
            'role_id' => 'sometimes|exists:roles,id',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }
}

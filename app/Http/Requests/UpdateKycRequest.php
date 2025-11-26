<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKycRequest extends FormRequest
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
            'status' => 'nullable|in:pending,approved,rejected',
            'rejected_reason' => 'nullable|string|required_if:status,rejected',
            'is_verified' => 'nullable|boolean',
            'verified_at' => 'nullable|date',
            'full_name' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:1000',
            'document_type' => 'nullable|in:passport,driving_license,id_card',
            // document_scan_copy may be optional on update
            'document_scan_copy' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'is_active' => 'nullable|boolean',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }
}

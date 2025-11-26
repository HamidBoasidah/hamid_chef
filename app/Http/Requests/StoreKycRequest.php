<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKycRequest extends FormRequest
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
            'user_id' => 'required|exists:users,id',
            'status' => 'nullable|in:pending,approved,rejected',
            'rejected_reason' => 'nullable|string|required_if:status,rejected',
            'is_verified' => 'nullable|boolean',
            'verified_at' => 'nullable|date',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'nullable|date',
            'address' => 'nullable|string|max:1000',
            'document_type' => 'required|in:passport,driving_license,id_card',
            // accept images and pdfs for document scans
            'document_scan_copy' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'is_active' => 'nullable|boolean',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }
}

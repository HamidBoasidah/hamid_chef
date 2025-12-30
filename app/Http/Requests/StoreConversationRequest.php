<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Authorization handled by sanctum middleware; allow here.
        return true;
    }

    public function rules(): array
    {
        return [
            'chef_id' => ['required', 'integer', 'exists:chefs,id'],
            // optional first message
            'content' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,txt'],
        ];
    }
}

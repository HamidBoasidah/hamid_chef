<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationByChefRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Protected by routes with sanctum + user_role:chef
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'content' => ['nullable', 'string', 'max:5000'],
            'attachment' => ['nullable', 'file', 'max:10240', 'mimes:jpeg,png,webp,gif,pdf,doc,docx,xls,xlsx,txt'],
        ];
    }
}

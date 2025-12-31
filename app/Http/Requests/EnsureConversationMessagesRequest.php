<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnsureConversationMessagesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // guarded by sanctum middleware
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'chef_id' => ['required', 'integer', 'exists:chefs,id'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}

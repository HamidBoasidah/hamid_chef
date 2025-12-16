<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UniqueChefForUser implements ValidationRule
{
    protected ?int $userId;

    public function __construct(?int $userId = null)
    {
        $this->userId = $userId ?? Auth::id();
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->userId) {
            return; // If no user is authenticated, skip validation
        }

        $existingChef = DB::table('chefs')
            ->where('user_id', $this->userId)
            ->whereNull('deleted_at') // Respect soft deletes
            ->exists();

        if ($existingChef) {
            $fail('المستخدم لديه بالفعل ملف طاهي');
        }
    }
}
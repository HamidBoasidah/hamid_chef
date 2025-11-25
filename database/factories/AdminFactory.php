<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Admin>
 */
class AdminFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'avatar' => null,
            'phone_number' => fake()->numerify('5########'),
            'whatsapp_number' => fake()->numerify('7########'),
            'address' => fake()->address(),
            'password' => static::$password ??= Hash::make('password'),
            'facebook' => null,
            'x_url' => null,
            'linkedin' => null,
            'instagram' => null,
            'is_active' => true,
            'locale' => 'ar',
            'created_by' => null,
            'updated_by' => null,
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function ($admin) {
            $role = \Spatie\Permission\Models\Role::where('name', 'admin')->first() ?? \Spatie\Permission\Models\Role::inRandomOrder()->first();
            if ($role) {
                $admin->assignRole($role->name);
            }
        });
    }
}

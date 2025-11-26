<?php

namespace Database\Factories;

use App\Models\Chef;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefFactory extends Factory
{
    protected $model = Chef::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()->chef(),
            'display_name' => $this->faker->name,
            'bio' => $this->faker->paragraph,
            'profile_image' => null,
            'city' => $this->faker->city,
            'area' => $this->faker->streetName,
            'base_hourly_rate' => $this->faker->randomFloat(2, 20, 200),
            'status' => $this->faker->randomElement(['pending', 'approved', 'suspended']),
            'rating_avg' => $this->faker->randomFloat(2, 3, 5),
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

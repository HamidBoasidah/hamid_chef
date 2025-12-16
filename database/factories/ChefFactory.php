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
            'name' => $this->faker->name,
            'short_description' => $this->faker->sentence,
            'long_description' => $this->faker->paragraphs(3, true),
            'email' => $this->faker->unique()->safeEmail,
            'phone' => $this->faker->phoneNumber,
            'address' => $this->faker->address,
            'governorate_id' => \App\Models\Governorate::factory(),
            'district_id' => \App\Models\District::factory(),
            'area_id' => \App\Models\Area::factory(),
            'base_hourly_rate' => $this->faker->randomFloat(2, 0, 500),
            'rating_avg' => $this->faker->randomFloat(2, 0, 5),
            'is_active' => $this->faker->boolean(80),
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

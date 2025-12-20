<?php

namespace Database\Factories;

use App\Models\ChefService;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefServiceFactory extends Factory
{
    protected $model = ChefService::class;

    public function definition()
    {
        $type = $this->faker->randomElement(['hourly', 'package']);
        return [
            'chef_id' => Chef::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'service_type' => $type,
            'hourly_rate' => $type === 'hourly' ? $this->faker->randomFloat(2, 20, 200) : null,
            'package_price' => $type === 'package' ? $this->faker->randomFloat(2, 50, 1000) : null,
            'min_hours' => $type === 'hourly' ? $this->faker->numberBetween(1,8) : null,
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

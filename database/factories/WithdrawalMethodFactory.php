<?php

namespace Database\Factories;

use App\Models\WithdrawalMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class WithdrawalMethodFactory extends Factory
{
    protected $model = WithdrawalMethod::class;

    public function definition()
    {
        return [
            'name' => $this->faker->unique()->word,
            'description' => $this->faker->sentence,
            'min_amount' => $this->faker->randomFloat(2, 10, 100),
            'max_amount' => $this->faker->randomFloat(2, 100, 1000),
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

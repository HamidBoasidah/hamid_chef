<?php

namespace Database\Factories;

use App\Models\ChefWalletTransaction;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefWalletTransactionFactory extends Factory
{
    protected $model = ChefWalletTransaction::class;

    public function definition()
    {
        return [
            'chef_id' => Chef::factory(),
            'type' => $this->faker->randomElement(['credit', 'debit']),
            'amount' => $this->faker->randomFloat(2, 5, 500),
            'balance' => $this->faker->randomFloat(2, 0, 2000),
            'description' => $this->faker->sentence,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

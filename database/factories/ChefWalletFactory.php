<?php

namespace Database\Factories;

use App\Models\ChefWallet;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefWalletFactory extends Factory
{
    protected $model = ChefWallet::class;

    public function definition()
    {
        return [
            'chef_id' => Chef::factory(),
            'balance' => $this->faker->randomFloat(2, 0, 2000),
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

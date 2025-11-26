<?php

namespace Database\Factories;

use App\Models\ChefWithdrawalRequest;
use App\Models\Chef;
use App\Models\WithdrawalMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefWithdrawalRequestFactory extends Factory
{
    protected $model = ChefWithdrawalRequest::class;

    public function definition()
    {
        return [
            'chef_id' => Chef::factory(),
            'withdrawal_method_id' => WithdrawalMethod::factory(),
            'amount' => $this->faker->randomFloat(2, 20, 1000),
            'status' => $this->faker->randomElement(['pending', 'processing', 'paid']),
            'requested_at' => now(),
            'processed_at' => null,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

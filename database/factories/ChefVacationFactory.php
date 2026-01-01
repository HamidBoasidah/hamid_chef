<?php

namespace Database\Factories;

use App\Models\ChefVacation;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ChefVacationFactory extends Factory
{
    protected $model = ChefVacation::class;

    public function definition(): array
    {
        $chef = Chef::factory();
        $date = Carbon::now()->addDays($this->faker->numberBetween(1, 20))->format('Y-m-d');

        return [
            'chef_id' => $chef,
            'date' => $date,
            'note' => $this->faker->optional()->sentence,
            'is_active' => true,
        ];
    }
}

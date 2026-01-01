<?php

namespace Database\Factories;

use App\Models\ChefWorkingHour;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefWorkingHourFactory extends Factory
{
    protected $model = ChefWorkingHour::class;

    public function definition()
    {
        return [
            'chef_id' => Chef::factory(),
            'day_of_week' => $this->faker->numberBetween(0, 6),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'is_active' => true,
        ];
    }

    /**
     * Morning shift state
     */
    public function morningShift()
    {
        return $this->state(function (array $attributes) {
            return [
                'start_time' => '09:00:00',
                'end_time' => '14:00:00',
            ];
        });
    }

    /**
     * Evening shift state
     */
    public function eveningShift()
    {
        return $this->state(function (array $attributes) {
            return [
                'start_time' => '16:00:00',
                'end_time' => '22:00:00',
            ];
        });
    }
}

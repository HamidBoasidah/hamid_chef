<?php

namespace Database\Factories;

use App\Models\ChefRating;
use App\Models\Booking;
use App\Models\User;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefRatingFactory extends Factory
{
    protected $model = ChefRating::class;

    public function definition()
    {
        $booking = Booking::factory();

        return [
            'booking_id' => $booking,
            'customer_id' => User::factory()->customer(),
            'chef_id' => Chef::factory(),
            'rating' => $this->faker->numberBetween(1,5),
            'review' => $this->faker->optional()->sentence,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

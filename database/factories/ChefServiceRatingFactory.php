<?php

namespace Database\Factories;

use App\Models\ChefServiceRating;
use App\Models\Booking;
use App\Models\User;
use App\Models\Chef;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChefServiceRatingFactory extends Factory
{
    protected $model = ChefServiceRating::class;

    public function definition()
    {
        $booking = Booking::factory();

        return [
            'booking_id' => $booking,
            'customer_id' => User::factory()->customer(),
            'chef_id' => Chef::factory(),
            'rating' => $this->faker->numberBetween(1,5),
            'review' => $this->faker->optional()->sentence,
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

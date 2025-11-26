<?php

namespace Database\Factories;

use App\Models\BookingTransaction;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingTransactionFactory extends Factory
{
    protected $model = BookingTransaction::class;

    public function definition()
    {
        return [
            'booking_id' => Booking::factory(),
            'transaction_id' => $this->faker->uuid,
            'payment_method' => $this->faker->randomElement(['card', 'apple_pay', 'mada']),
            'amount' => $this->faker->randomFloat(2, 20, 500),
            'currency' => 'SAR',
            'raw_response' => json_encode(['status' => 'success']),
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

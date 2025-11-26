<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\User;
use App\Models\Chef;
use App\Models\ChefService;
use App\Models\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition()
    {
        $customer = User::factory()->customer();
        $chef = Chef::factory();
        $service = ChefService::factory()->for($chef, 'chef');

        $date = $this->faker->dateTimeBetween('-1 months', '+1 months');
        $startTime = $this->faker->time();

        $serviceType = $this->faker->randomElement(['hourly', 'package']);

        // determine unit price from service if available, otherwise random
        $unitPrice = $service->hourly_rate ?? $service->package_price ?? $this->faker->randomFloat(2, 20, 200);

        $hours = $serviceType === 'hourly' ? $this->faker->numberBetween(1, 6) : 1;
        $numberOfGuests = $this->faker->numberBetween(1, 10);

        $extraGuestsCount = 0;
        $extraGuestsAmount = 0;

        $total = round($unitPrice * $hours + $extraGuestsAmount, 2);

        return [
            'customer_id' => $customer,
            'chef_id' => $chef,
            'chef_service_id' => $service,
            'address_id' => Address::factory()->for($customer, 'user'),
            'date' => $date->format('Y-m-d'),
            'start_time' => $startTime,
            'hours_count' => $hours,
            'number_of_guests' => $numberOfGuests,
            'service_type' => $serviceType,
            'unit_price' => $unitPrice,
            'extra_guests_count' => $extraGuestsCount,
            'extra_guests_amount' => $extraGuestsAmount,
            'total_amount' => $total,
            'commission_amount' => round($total * 0.15, 2),
            'payment_status' => $this->faker->randomElement(['pending', 'paid', 'refunded', 'failed']),
            'booking_status' => $this->faker->randomElement(['pending', 'accepted', 'rejected', 'cancelled_by_customer', 'cancelled_by_chef', 'completed']),
            'notes' => $this->faker->optional()->sentence,
            'is_active' => true,
            'created_by' => null,
            'updated_by' => null,
        ];
    }
}

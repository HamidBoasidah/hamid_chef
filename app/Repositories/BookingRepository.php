<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\Eloquent\BaseRepository;

class BookingRepository extends BaseRepository
{
    protected array $defaultWith = [
        'customer' => ['id', 'first_name', 'last_name'],
            'chef' => ['id', 'user_id', 'name', 'address'],
        'service' => ['id', 'chef_id', 'name', 'slug'],
        'address' => ['id', 'user_id', 'address'],
        'transactions' => ['id', 'booking_id', 'amount', 'currency', 'payment_method'],
        'rating' => ['id', 'booking_id', 'rating'],
    ];

    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }
}

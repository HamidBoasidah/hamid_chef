<?php

namespace App\Repositories;

use App\Models\ChefServiceRating;
use App\Repositories\Eloquent\BaseRepository;

class ChefServiceRatingRepository extends BaseRepository
{
    protected array $defaultWith = [
        'booking',
        'chef',
        'customer',
    ];

    public function __construct(ChefServiceRating $model)
    {
        parent::__construct($model);
    }

    /**
     * Get ratings for a specific chef
     */
    public function getForChef(int $chefId, ?array $with = null)
    {
        return $this->makeQuery($with)
            ->where('chef_id', $chefId)
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    /**
     * Get ratings for a specific customer
     */
    public function getForCustomer(int $customerId, ?array $with = null)
    {
        return $this->makeQuery($with)
            ->where('customer_id', $customerId)
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    /**
     * Get rating for a specific booking
     */
    public function getForBooking(int $bookingId, ?array $with = null)
    {
        return $this->makeQuery($with)
            ->where('booking_id', $bookingId)
            ->where('is_active', true)
            ->first();
    }

    /**
     * Check if customer has already rated a booking
     */
    public function hasCustomerRatedBooking(int $customerId, int $bookingId): bool
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->where('booking_id', $bookingId)
            ->where('is_active', true)
            ->exists();
    }
}

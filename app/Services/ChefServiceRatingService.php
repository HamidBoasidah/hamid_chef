<?php

namespace App\Services;

use App\Repositories\ChefServiceRatingRepository;
use App\DTOs\ChefServiceRatingDTO;
use App\Models\ChefServiceRating;

class ChefServiceRatingService
{
    protected ChefServiceRatingRepository $ratings;

    public function __construct(ChefServiceRatingRepository $ratings)
    {
        $this->ratings = $ratings;
    }

    public function all(array $with = [])
    {
        return $this->ratings->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->ratings->paginate($perPage, $with);
    }

    public function find($id, array $with = [])
    {
        return $this->ratings->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        return $this->ratings->create($attributes);
    }

    public function update($id, array $attributes)
    {
        return $this->ratings->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->ratings->delete($id);
    }

    public function activate($id)
    {
        return $this->ratings->activate($id);
    }

    public function deactivate($id)
    {
        return $this->ratings->deactivate($id);
    }

    /**
     * Get ratings for a specific chef
     */
    public function getForChef(int $chefId, ?array $with = null)
    {
        return $this->ratings->getForChef($chefId, $with);
    }

    /**
     * Get ratings for a specific customer
     */
    public function getForCustomer(int $customerId, ?array $with = null)
    {
        return $this->ratings->getForCustomer($customerId, $with);
    }

    /**
     * Get rating for a specific booking
     */
    public function getForBooking(int $bookingId, ?array $with = null)
    {
        return $this->ratings->getForBooking($bookingId, $with);
    }

    /**
     * Check if customer can rate a booking
     */
    public function canCustomerRateBooking(int $customerId, int $bookingId): bool
    {
        // Check if customer has already rated this booking
        if ($this->ratings->hasCustomerRatedBooking($customerId, $bookingId)) {
            return false;
        }

        // Additional logic can be added here to check booking status
        return true;
    }

    /**
     * Create rating for a booking
     */
    public function createForBooking(array $attributes): ChefServiceRating
    {
        // Validate that customer can rate this booking
        if (!$this->canCustomerRateBooking($attributes['customer_id'], $attributes['booking_id'])) {
            throw new \Exception('Customer has already rated this booking or is not allowed to rate it.');
        }

        return $this->ratings->create($attributes);
    }

    /**
     * Update rating by customer (only if they own it)
     */
    public function updateByCustomer(int $ratingId, int $customerId, array $attributes): ChefServiceRating
    {
        $rating = $this->ratings->findOrFail($ratingId);
        
        if ($rating->customer_id !== $customerId) {
            throw new \Exception('Customer can only update their own ratings.');
        }

        return $this->ratings->update($ratingId, $attributes);
    }

    /**
     * Delete rating by customer (only if they own it)
     */
    public function deleteByCustomer(int $ratingId, int $customerId): bool
    {
        $rating = $this->ratings->findOrFail($ratingId);
        
        if ($rating->customer_id !== $customerId) {
            throw new \Exception('Customer can only delete their own ratings.');
        }

        return $this->ratings->delete($ratingId);
    }

    /**
     * Find rating for a specific customer (throws exception if not found or not owned)
     */
    public function findForCustomer(int $ratingId, int $customerId, array $with = [])
    {
        $rating = $this->ratings->findOrFail($ratingId, $with);
        
        if ($rating->customer_id !== $customerId) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Rating not found for this customer.');
        }

        return $rating;
    }

    /**
     * Update an existing model instance (without re-querying)
     */
    public function updateModel(ChefServiceRating $rating, array $attributes): ChefServiceRating
    {
        $rating->fill($attributes);
        $rating->save();
        
        // Reload relationships if needed
        if ($rating->relationLoaded('booking')) {
            $rating->load('booking');
        }
        if ($rating->relationLoaded('chef')) {
            $rating->load('chef');
        }
        if ($rating->relationLoaded('customer')) {
            $rating->load('customer');
        }

        return $rating;
    }
}

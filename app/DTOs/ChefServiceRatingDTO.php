<?php

namespace App\DTOs;

use App\Models\ChefServiceRating;

class ChefServiceRatingDTO extends BaseDTO
{
    public $id;
    public $booking_id;
    public $customer_id;
    public $chef_id;
    public $rating;
    public $review;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    // Relationships
    public $booking;
    public $customer;
    public $chef;

    public function __construct(
        $id,
        $booking_id,
        $customer_id,
        $chef_id,
        $rating,
        $review,
        $is_active,
        $created_by,
        $updated_by,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->booking_id = $booking_id;
        $this->customer_id = $customer_id;
        $this->chef_id = $chef_id;
        $this->rating = $rating;
        $this->review = $review;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefServiceRating $rating): self
    {
        $dto = new self(
            $rating->id,
            $rating->booking_id ?? null,
            $rating->customer_id ?? null,
            $rating->chef_id ?? null,
            $rating->rating ?? null,
            $rating->review ?? null,
            $rating->is_active ?? true,
            $rating->created_by ?? null,
            $rating->updated_by ?? null,
            $rating->created_at?->toDateTimeString() ?? null,
            $rating->deleted_at?->toDateTimeString() ?? null,
        );

        // Add relationships if loaded
        if ($rating->relationLoaded('booking')) {
            $dto->booking = $rating->booking;
        }
        if ($rating->relationLoaded('customer')) {
            $dto->customer = $rating->customer;
        }
        if ($rating->relationLoaded('chef')) {
            $dto->chef = $rating->chef;
        }

        return $dto;
    }

    public function toArray(): array
    {
        $array = [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'customer_id' => $this->customer_id,
            'chef_id' => $this->chef_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
        ];

        // Add relationships if they exist
        if (isset($this->booking)) {
            $array['booking'] = $this->booking;
        }
        if (isset($this->customer)) {
            $array['customer'] = $this->customer;
        }
        if (isset($this->chef)) {
            $array['chef'] = $this->chef;
        }

        return $array;
    }

    public function toIndexArray(): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'customer_id' => $this->customer_id,
            'chef_id' => $this->chef_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'created_at' => $this->created_at,
        ];
    }
}

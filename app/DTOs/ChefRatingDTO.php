<?php

namespace App\DTOs;

use App\Models\ChefRating;

class ChefRatingDTO extends BaseDTO
{
    public $id;
    public $booking_id;
    public $customer_id;
    public $chef_id;
    public $rating;
    public $review;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct(
        $id,
        $booking_id,
        $customer_id,
        $chef_id,
        $rating,
        $review,
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
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefRating $rating): self
    {
        return new self(
            $rating->id,
            $rating->booking_id ?? null,
            $rating->customer_id ?? null,
            $rating->chef_id ?? null,
            $rating->rating ?? null,
            $rating->review ?? null,
            $rating->created_by ?? null,
            $rating->updated_by ?? null,
            $rating->created_at?->toDateTimeString() ?? null,
            $rating->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'customer_id' => $this->customer_id,
            'chef_id' => $this->chef_id,
            'rating' => $this->rating,
            'review' => $this->review,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
        ];
    }

    public function toIndexArray(): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'chef_id' => $this->chef_id,
            'rating' => $this->rating,
        ];
    }
}

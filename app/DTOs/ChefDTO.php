<?php

namespace App\DTOs;

use App\Models\Chef;

class ChefDTO extends BaseDTO
{
    public $id;
    public $user_id;
    public $display_name;
    public $bio;
    public $profile_image;
    public $city;
    public $area;
    public $base_hourly_rate;
    public $status;
    public $rating_avg;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct(
        $id,
        $user_id,
        $display_name,
        $bio,
        $profile_image,
        $city,
        $area,
        $base_hourly_rate,
        $status,
        $rating_avg,
        $is_active,
        $created_by,
        $updated_by,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->display_name = $display_name;
        $this->bio = $bio;
        $this->profile_image = $profile_image;
        $this->city = $city;
        $this->area = $area;
        $this->base_hourly_rate = $base_hourly_rate;
        $this->status = $status;
        $this->rating_avg = $rating_avg;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(Chef $chef): self
    {
        return new self(
            $chef->id,
            $chef->user_id ?? null,
            $chef->display_name ?? null,
            $chef->bio ?? null,
            $chef->profile_image ?? null,
            $chef->city ?? null,
            $chef->area ?? null,
            $chef->base_hourly_rate ?? 0,
            $chef->status ?? null,
            $chef->rating_avg ?? 0,
            $chef->is_active ?? true,
            $chef->created_by ?? null,
            $chef->updated_by ?? null,
            $chef->created_at?->toDateTimeString() ?? null,
            $chef->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'display_name' => $this->display_name,
            'bio' => $this->bio,
            'profile_image' => $this->profile_image,
            'city' => $this->city,
            'area' => $this->area,
            'base_hourly_rate' => $this->base_hourly_rate,
            'status' => $this->status,
            'rating_avg' => $this->rating_avg,
            'is_active' => $this->is_active,
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
            'display_name' => $this->display_name,
            'city' => $this->city,
            'area' => $this->area,
            'rating_avg' => $this->rating_avg,
            'is_active' => $this->is_active,
        ];
    }
}

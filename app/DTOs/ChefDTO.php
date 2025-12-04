<?php

namespace App\DTOs;

use App\Models\Chef;

class ChefDTO extends BaseDTO
{
    public $id;
    public $user_id;
    public $name;
    public $address;
    public $bio;
    public $short_description;
    public $long_description;
    public $logo;
    public $banner;
    public $email;
    public $phone;
    public $governorate_id;
    public $governorate_name_ar;
    public $governorate_name_en;
    public $district_id;
    public $district_name_ar;
    public $district_name_en;
    public $area_id;
    public $area_name_ar;
    public $area_name_en;
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
        $name,
        $address,
        $bio,
        $short_description = null,
        $long_description = null,
        $logo,
        $banner,
        $governorate_id,
        $governorate_name_ar = null,
        $governorate_name_en = null,
        $district_id,
        $district_name_ar = null,
        $district_name_en = null,
        $area_id,
        $area_name_ar = null,
        $area_name_en = null,
        $base_hourly_rate,
        $status,
        $rating_avg,
        $is_active,
        $created_by,
        $updated_by,
        $email = null,
        $phone = null,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->name = $name;
        $this->address = $address;
        $this->bio = $bio;
        $this->short_description = $short_description;
        $this->long_description = $long_description;
        $this->logo = $logo;
        $this->banner = $banner;
        $this->email = $email;
        $this->phone = $phone;
        $this->governorate_id = $governorate_id;
        $this->governorate_name_ar = $governorate_name_ar;
        $this->governorate_name_en = $governorate_name_en;
        $this->district_id = $district_id;
        $this->district_name_ar = $district_name_ar;
        $this->district_name_en = $district_name_en;
        $this->area_id = $area_id;
        $this->area_name_ar = $area_name_ar;
        $this->area_name_en = $area_name_en;
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
            $chef->name ?? null,
            $chef->address ?? null,
            // legacy bio (kept for compatibility)
            $chef->bio ?? null,
            // explicit short/long descriptions
            $chef->short_description ?? null,
            $chef->long_description ?? null,
            $chef->logo ?? null,
            $chef->banner ?? null,
            $chef->governorate_id ?? null,
            // governorate names (if relation loaded)
            $chef->governorate?->name_ar ?? null,
            $chef->governorate?->name_en ?? null,
            $chef->district_id ?? null,
            // district names (if relation loaded)
            $chef->district?->name_ar ?? null,
            $chef->district?->name_en ?? null,
            $chef->area_id ?? null,
            // area names (if relation loaded)
            $chef->area?->name_ar ?? null,
            $chef->area?->name_en ?? null,
            $chef->base_hourly_rate ?? 0,
            $chef->status ?? null,
            $chef->rating_avg ?? 0,
            $chef->is_active ?? true,
            $chef->created_by ?? null,
            $chef->updated_by ?? null,
            // contact fields
            $chef->email ?? null,
            $chef->phone ?? null,
            $chef->created_at?->toDateTimeString() ?? null,
            $chef->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'address' => $this->address,
            'bio' => $this->bio,
            'logo' => $this->logo,
            'short_description' => $this->short_description,
            'long_description' => $this->long_description,
            'banner' => $this->banner,
            'governorate_id' => $this->governorate_id,
            'governorate_name_ar' => $this->governorate_name_ar,
            'governorate_name_en' => $this->governorate_name_en,
            'district_id' => $this->district_id,
            'district_name_ar' => $this->district_name_ar,
            'district_name_en' => $this->district_name_en,
            'area_id' => $this->area_id,
            'area_name_ar' => $this->area_name_ar,
            'area_name_en' => $this->area_name_en,
            'base_hourly_rate' => $this->base_hourly_rate,
            'email' => $this->email,
            'phone' => $this->phone,
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
            'name' => $this->name,
            'address' => $this->address,
            'logo' => $this->logo,
            'governorate_id' => $this->governorate_id,
            'governorate_name_ar' => $this->governorate_name_ar,
            'governorate_name_en' => $this->governorate_name_en,
            'district_id' => $this->district_id,
            'district_name_ar' => $this->district_name_ar,
            'district_name_en' => $this->district_name_en,
            'area_id' => $this->area_id,
            'area_name_ar' => $this->area_name_ar,
            'area_name_en' => $this->area_name_en,
            'rating_avg' => $this->rating_avg,
            'is_active' => $this->is_active,
        ];
    }
}

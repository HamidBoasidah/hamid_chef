<?php

namespace App\DTOs;

use App\Models\Address;

class AddressDTO extends BaseDTO
{
    public $id;
    public $user_id;
    public $label;
    public $address;
    public $governorate_id;
    public $district_id;
    public $area_id;
    public $latitude;
    public $longitude;
    public $building_number;
    public $floor_number;
    public $apartment_number;
    public $is_default;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct(
        $id,
        $user_id,
        $label,
        $address,
        $governorate_id,
        $district_id,
        $area_id,
        $latitude,
        $longitude,
        $building_number,
        $floor_number,
        $apartment_number,
        $is_default,
        $is_active,
        $created_by,
        $updated_by,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->label = $label;
        $this->address = $address;
        $this->governorate_id = $governorate_id;
        $this->district_id = $district_id;
        $this->area_id = $area_id;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->building_number = $building_number;
        $this->floor_number = $floor_number;
        $this->apartment_number = $apartment_number;
        $this->is_default = (bool) $is_default;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(Address $address): self
    {
        return new self(
            $address->id,
            $address->user_id ?? null,
            $address->label ?? null,
            $address->address ?? null,
            $address->governorate_id ?? null,
            $address->district_id ?? null,
            $address->area_id ?? null,
            $address->latitude ?? null,
            $address->longitude ?? null,
            $address->building_number ?? null,
            $address->floor_number ?? null,
            $address->apartment_number ?? null,
            $address->is_default ?? false,
            $address->is_active ?? true,
            $address->created_by ?? null,
            $address->updated_by ?? null,
            $address->created_at?->toDateTimeString() ?? null,
            $address->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'label' => $this->label,
            'address' => $this->address,
            'governorate_id' => $this->governorate_id,
            'district_id' => $this->district_id,
            'area_id' => $this->area_id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'building_number' => $this->building_number,
            'floor_number' => $this->floor_number,
            'apartment_number' => $this->apartment_number,
            'is_default' => $this->is_default,
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
            'label' => $this->label,
            'address' => $this->address,
            'is_default' => $this->is_default,
        ];
    }
}

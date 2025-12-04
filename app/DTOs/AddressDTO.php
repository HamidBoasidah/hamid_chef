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
    public $street;
    public $building_number;
    public $floor_number;
    public $apartment_number;
    public $is_default;
    public $is_active;
    public $governorate_name_ar;
    public $governorate_name_en;
    public $district_name_ar;
    public $district_name_en;
    public $area_name_ar;
    public $area_name_en;
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
        $street,
        $building_number,
        $floor_number,
        $apartment_number,
        $is_default,
        $is_active,
        $created_by,
        $updated_by,
        $governorate_name_ar = null,
        $governorate_name_en = null,
        $district_name_ar = null,
        $district_name_en = null,
        $area_name_ar = null,
        $area_name_en = null,
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
        $this->street = $street;
        $this->building_number = $building_number;
        $this->floor_number = $floor_number;
        $this->apartment_number = $apartment_number;
        $this->is_default = (bool) $is_default;
        $this->is_active = (bool) $is_active;
        $this->governorate_name_ar = $governorate_name_ar;
        $this->governorate_name_en = $governorate_name_en;
        $this->district_name_ar = $district_name_ar;
        $this->district_name_en = $district_name_en;
        $this->area_name_ar = $area_name_ar;
        $this->area_name_en = $area_name_en;
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
            $address->street ?? null,
            $address->building_number ?? null,
            $address->floor_number ?? null,
            $address->apartment_number ?? null,
            $address->is_default ?? false,
            $address->is_active ?? true,
            // created_by, updated_by
            $address->created_by ?? null,
            $address->updated_by ?? null,
            // names from relations (if loaded)
            $address->governorate?->name_ar ?? null,
            $address->governorate?->name_en ?? null,
            $address->district?->name_ar ?? null,
            $address->district?->name_en ?? null,
            $address->area?->name_ar ?? null,
            $address->area?->name_en ?? null,
            // timestamps
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
            'governorate_name_ar' => $this->governorate_name_ar,
            'governorate_name_en' => $this->governorate_name_en,
            'district_name_ar' => $this->district_name_ar,
            'district_name_en' => $this->district_name_en,
            'area_name_ar' => $this->area_name_ar,
            'area_name_en' => $this->area_name_en,
            'street' => $this->street,
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
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'governorate_name_ar' => $this->governorate_name_ar,
            'governorate_name_en' => $this->governorate_name_en,
            'district_name_ar' => $this->district_name_ar,
            'district_name_en' => $this->district_name_en,
            'area_name_ar' => $this->area_name_ar,
            'area_name_en' => $this->area_name_en,
        ];
    }
}

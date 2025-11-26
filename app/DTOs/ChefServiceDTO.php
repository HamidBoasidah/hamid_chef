<?php

namespace App\DTOs;

use App\Models\ChefService;

class ChefServiceDTO extends BaseDTO
{
    public $id;
    public $chef_id;
    public $name;
    public $slug;
    public $description;
    public $service_type;
    public $hourly_rate;
    public $min_hours;
    public $package_price;
    public $max_guests_included;
    public $allow_extra_guests;
    public $extra_guest_price;
    public $max_guests_allowed;
    public $max_guests;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct(
        $id,
        $chef_id,
        $name,
        $slug,
        $description,
        $service_type,
        $hourly_rate,
        $min_hours,
        $package_price,
        $max_guests_included,
        $allow_extra_guests,
        $extra_guest_price,
        $max_guests_allowed,
        $max_guests,
        $is_active,
        $created_by,
        $updated_by,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->chef_id = $chef_id;
        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->service_type = $service_type;
        $this->hourly_rate = $hourly_rate;
        $this->min_hours = $min_hours;
        $this->package_price = $package_price;
        $this->max_guests_included = $max_guests_included;
        $this->allow_extra_guests = (bool) $allow_extra_guests;
        $this->extra_guest_price = $extra_guest_price;
        $this->max_guests_allowed = $max_guests_allowed;
        $this->max_guests = $max_guests;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefService $service): self
    {
        return new self(
            $service->id,
            $service->chef_id ?? null,
            $service->name ?? null,
            $service->slug ?? null,
            $service->description ?? null,
            $service->service_type ?? null,
            $service->hourly_rate ?? null,
            $service->min_hours ?? null,
            $service->package_price ?? null,
            $service->max_guests_included ?? null,
            $service->allow_extra_guests ?? false,
            $service->extra_guest_price ?? 0,
            $service->max_guests_allowed ?? null,
            $service->max_guests ?? null,
            $service->is_active ?? true,
            $service->created_by ?? null,
            $service->updated_by ?? null,
            $service->created_at?->toDateTimeString() ?? null,
            $service->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->chef_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'service_type' => $this->service_type,
            'hourly_rate' => $this->hourly_rate,
            'min_hours' => $this->min_hours,
            'package_price' => $this->package_price,
            'max_guests_included' => $this->max_guests_included,
            'allow_extra_guests' => $this->allow_extra_guests,
            'extra_guest_price' => $this->extra_guest_price,
            'max_guests_allowed' => $this->max_guests_allowed,
            'max_guests' => $this->max_guests,
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
            'chef_id' => $this->chef_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'service_type' => $this->service_type,
            'is_active' => $this->is_active,
        ];
    }
}

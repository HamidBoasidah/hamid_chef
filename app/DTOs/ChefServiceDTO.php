<?php

namespace App\DTOs;

use App\Models\ChefService;

class ChefServiceDTO extends BaseDTO
{
    public $id;
    public $chef_id;
    public $chef_name;
    public $chef_logo;
    public $name;
    public $description;
    public $service_type;
    public $hourly_rate;
    public $min_hours;
    public $package_price;
    public $max_guests_included;
    public $allow_extra_guests;
    public $extra_guest_price;
    public $feature_image;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;
    public $tags;
    public $images;

    public function __construct(
        $id,
        $chef_id,
        $name,
        $service_type,
        $chef_name = null,
        $chef_logo = null,
        $description = null,
        $hourly_rate = null,
        $min_hours = null,
        $package_price = null,
        $max_guests_included = null,
        $allow_extra_guests = false,
        $extra_guest_price = null,
        $feature_image = null,
        $is_active = true,
        $created_by = null,
        $updated_by = null,
        $created_at = null,
        $deleted_at = null,
        $tags = [],
        $images = []
    ) {
        $this->id = $id;
        $this->chef_id = $chef_id;
        $this->name = $name;
        $this->service_type = $service_type;
        $this->chef_name = $chef_name;
        $this->chef_logo = $chef_logo;
        $this->description = $description;
        $this->hourly_rate = $hourly_rate;
        $this->min_hours = $min_hours;
        $this->package_price = $package_price;
        $this->max_guests_included = $max_guests_included;
        $this->allow_extra_guests = (bool) $allow_extra_guests;
        $this->extra_guest_price = $extra_guest_price;
        $this->feature_image = $feature_image;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
        $this->tags = $tags;
        $this->images = $images;
    }

    public static function fromModel(ChefService $service): self
    {
        return new self(
            $service->id,
            $service->chef_id ?? null,
            $service->name ?? null,
            $service->service_type ?? null,
            // chef info (if relation loaded)
            $service->chef?->name ?? null,
            $service->chef?->logo ?? null,
            $service->description ?? null,
            $service->hourly_rate ?? null,
            $service->min_hours ?? null,
            $service->package_price ?? null,
            $service->max_guests_included ?? null,
            $service->allow_extra_guests ?? false,
            $service->extra_guest_price ?? null,
            $service->feature_image ?? null,
            $service->is_active ?? true,
            $service->created_by ?? null,
            $service->updated_by ?? null,
            $service->created_at?->toDateTimeString() ?? null,
            $service->deleted_at?->toDateTimeString() ?? null,
            // tags (if relation loaded)
            $service->relationLoaded('tags') ? $service->tags->map(function ($tag) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                    'is_active' => $tag->pivot?->is_active ?? true,
                ];
            })->toArray() : [],
            // images (if relation loaded)
            $service->relationLoaded('images') ? $service->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image' => $image->image,
                    'is_active' => $image->is_active,
                    'created_at' => $image->created_at?->toDateTimeString(),
                ];
            })->toArray() : [],
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->chef_id,
            'chef_name' => $this->chef_name,
            'chef_logo' => $this->chef_logo,
            'name' => $this->name,
            'description' => $this->description,
            'service_type' => $this->service_type,
            'hourly_rate' => $this->hourly_rate,
            'min_hours' => $this->min_hours,
            'package_price' => $this->package_price,
            'max_guests_included' => $this->max_guests_included,
            'allow_extra_guests' => $this->allow_extra_guests,
            'extra_guest_price' => $this->extra_guest_price,
            'feature_image' => $this->feature_image,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
            'tags' => $this->tags,
            'images' => $this->images,
        ];
    }

    public function toIndexArray(): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->chef_id,
            'chef_name' => $this->chef_name,
            'chef_logo' => $this->chef_logo,
            'name' => $this->name,
            'service_type' => $this->service_type,
            'hourly_rate' => $this->hourly_rate,
            'package_price' => $this->package_price,
            'feature_image' => $this->feature_image,
            'is_active' => $this->is_active,
            'tags' => $this->tags,
            // images excluded from index for performance
        ];
    }
}
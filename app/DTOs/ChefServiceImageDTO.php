<?php

namespace App\DTOs;

use App\Models\ChefServiceImage;

class ChefServiceImageDTO extends BaseDTO
{
    public $id;
    public $chef_service_id;
    public $image;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct($id, $chef_service_id, $image, $is_active, $created_by, $updated_by, $created_at = null, $deleted_at = null)
    {
        $this->id = $id;
        $this->chef_service_id = $chef_service_id;
        $this->image = $image;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefServiceImage $image): self
    {
        return new self(
            $image->id,
            $image->chef_service_id ?? null,
            $image->image ?? null,
            $image->is_active ?? true,
            $image->created_by ?? null,
            $image->updated_by ?? null,
            $image->created_at?->toDateTimeString() ?? null,
            $image->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_service_id' => $this->chef_service_id,
            'image' => $this->image,
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
            'chef_service_id' => $this->chef_service_id,
            'image' => $this->image,
            'is_active' => $this->is_active,
        ];
    }
}

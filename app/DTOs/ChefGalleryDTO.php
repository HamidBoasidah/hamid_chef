<?php

namespace App\DTOs;

use App\Models\ChefGallery;

class ChefGalleryDTO extends BaseDTO
{
    public $id;
    public $chef_id;
    public $image;
    public $caption;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct($id, $chef_id, $image, $caption, $is_active, $created_by, $updated_by, $created_at = null, $deleted_at = null)
    {
        $this->id = $id;
        $this->chef_id = $chef_id;
        $this->image = $image;
        $this->caption = $caption;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefGallery $gallery): self
    {
        return new self(
            $gallery->id,
            $gallery->chef_id ?? null,
            $gallery->image ?? null,
            $gallery->caption ?? null,
            $gallery->is_active ?? true,
            $gallery->created_by ?? null,
            $gallery->updated_by ?? null,
            $gallery->created_at?->toDateTimeString() ?? null,
            $gallery->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->chef_id,
            'image' => $this->image,
            'caption' => $this->caption,
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
            'image' => $this->image,
            'caption' => $this->caption,
        ];
    }
}

<?php

namespace App\DTOs;

use App\Models\ChefCategory;

class ChefCategoryDTO extends BaseDTO
{
    public $id;
    public $chef_id;
    public $cuisine_id;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct($id, $chef_id, $cuisine_id, $created_by, $updated_by, $created_at = null, $deleted_at = null)
    {
        $this->id = $id;
        $this->chef_id = $chef_id;
        $this->cuisine_id = $cuisine_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefCategory $chefCategory): self
    {
        return new self(
            $chefCategory->id,
            $chefCategory->chef_id ?? null,
            $chefCategory->cuisine_id ?? null,
            $chefCategory->created_by ?? null,
            $chefCategory->updated_by ?? null,
            $chefCategory->created_at?->toDateTimeString() ?? null,
            $chefCategory->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->chef_id,
            'cuisine_id' => $this->cuisine_id,
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
            'cuisine_id' => $this->cuisine_id,
        ];
    }
}

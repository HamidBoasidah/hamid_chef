<?php

namespace App\DTOs;

use App\Models\ChefServiceTag;

class ChefServiceTagDTO extends BaseDTO
{
    public $id;
    public $chef_service_id;
    public $tag_id;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct($id, $chef_service_id, $tag_id, $created_by, $updated_by, $created_at = null, $deleted_at = null)
    {
        $this->id = $id;
        $this->chef_service_id = $chef_service_id;
        $this->tag_id = $tag_id;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefServiceTag $pivot): self
    {
        return new self(
            $pivot->id,
            $pivot->chef_service_id ?? null,
            $pivot->tag_id ?? null,
            $pivot->created_by ?? null,
            $pivot->updated_by ?? null,
            $pivot->created_at?->toDateTimeString() ?? null,
            $pivot->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_service_id' => $this->chef_service_id,
            'tag_id' => $this->tag_id,
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
            'tag_id' => $this->tag_id,
        ];
    }
}

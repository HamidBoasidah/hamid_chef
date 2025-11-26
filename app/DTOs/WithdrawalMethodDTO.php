<?php

namespace App\DTOs;

use App\Models\WithdrawalMethod;

class WithdrawalMethodDTO extends BaseDTO
{
    public $id;
    public $name;
    public $description;
    public $min_amount;
    public $max_amount;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct($id, $name, $description, $min_amount, $max_amount, $is_active, $created_by, $updated_by, $created_at = null, $deleted_at = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->min_amount = $min_amount;
        $this->max_amount = $max_amount;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(WithdrawalMethod $method): self
    {
        return new self(
            $method->id,
            $method->name ?? null,
            $method->description ?? null,
            $method->min_amount ?? 0,
            $method->max_amount ?? 0,
            $method->is_active ?? false,
            $method->created_by ?? null,
            $method->updated_by ?? null,
            $method->created_at?->toDateTimeString() ?? null,
            $method->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
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
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'is_active' => $this->is_active,
        ];
    }
}

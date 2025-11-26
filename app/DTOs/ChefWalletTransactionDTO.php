<?php

namespace App\DTOs;

use App\Models\ChefWalletTransaction;

class ChefWalletTransactionDTO extends BaseDTO
{
    public $id;
    public $chef_id;
    public $type;
    public $amount;
    public $balance;
    public $description;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct(
        $id,
        $chef_id,
        $type,
        $amount,
        $balance,
        $description,
        $is_active,
        $created_by,
        $updated_by,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->chef_id = $chef_id;
        $this->type = $type;
        $this->amount = $amount;
        $this->balance = $balance;
        $this->description = $description;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefWalletTransaction $tx): self
    {
        return new self(
            $tx->id,
            $tx->chef_id ?? null,
            $tx->type ?? null,
            $tx->amount ?? 0,
            $tx->balance ?? 0,
            $tx->description ?? null,
            $tx->is_active ?? true,
            $tx->created_by ?? null,
            $tx->updated_by ?? null,
            $tx->created_at?->toDateTimeString() ?? null,
            $tx->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->chef_id,
            'type' => $this->type,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'description' => $this->description,
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
            'type' => $this->type,
            'amount' => $this->amount,
            'balance' => $this->balance,
        ];
    }
}

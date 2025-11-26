<?php

namespace App\DTOs;

use App\Models\ChefWallet;

class ChefWalletDTO extends BaseDTO
{
    public $id;
    public $chef_id;
    public $balance;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct($id, $chef_id, $balance, $created_by, $updated_by, $created_at = null, $deleted_at = null)
    {
        $this->id = $id;
        $this->chef_id = $chef_id;
        $this->balance = $balance;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefWallet $wallet): self
    {
        return new self(
            $wallet->id,
            $wallet->chef_id ?? null,
            $wallet->balance ?? 0,
            $wallet->created_by ?? null,
            $wallet->updated_by ?? null,
            $wallet->created_at?->toDateTimeString() ?? null,
            $wallet->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->chef_id,
            'balance' => $this->balance,
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
            'balance' => $this->balance,
        ];
    }
}

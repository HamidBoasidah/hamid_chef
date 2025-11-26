<?php

namespace App\DTOs;

use App\Models\ChefWithdrawalRequest;

class ChefWithdrawalRequestDTO extends BaseDTO
{
    public $id;
    public $chef_id;
    public $withdrawal_method_id;
    public $amount;
    public $status;
    public $requested_at;
    public $processed_at;
    public $payment_details;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct(
        $id,
        $chef_id,
        $withdrawal_method_id,
        $amount,
        $status,
        $requested_at,
        $processed_at,
        $payment_details,
        $is_active,
        $created_by,
        $updated_by,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->chef_id = $chef_id;
        $this->withdrawal_method_id = $withdrawal_method_id;
        $this->amount = $amount;
        $this->status = $status;
        $this->requested_at = $requested_at;
        $this->processed_at = $processed_at;
        $this->payment_details = $payment_details;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(ChefWithdrawalRequest $req): self
    {
        return new self(
            $req->id,
            $req->chef_id ?? null,
            $req->withdrawal_method_id ?? null,
            $req->amount ?? 0,
            $req->status ?? null,
            $req->requested_at?->toDateTimeString() ?? null,
            $req->processed_at?->toDateTimeString() ?? null,
            $req->payment_details ?? null,
            $req->is_active ?? true,
            $req->created_by ?? null,
            $req->updated_by ?? null,
            $req->created_at?->toDateTimeString() ?? null,
            $req->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'chef_id' => $this->chef_id,
            'withdrawal_method_id' => $this->withdrawal_method_id,
            'amount' => $this->amount,
            'status' => $this->status,
            'requested_at' => $this->requested_at,
            'processed_at' => $this->processed_at,
            'payment_details' => $this->payment_details,
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
            'amount' => $this->amount,
            'status' => $this->status,
        ];
    }
}

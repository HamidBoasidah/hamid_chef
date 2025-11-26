<?php

namespace App\DTOs;

use App\Models\BookingTransaction;

class BookingTransactionDTO extends BaseDTO
{
    public $id;
    public $booking_id;
    public $transaction_id;
    public $payment_method;
    public $amount;
    public $currency;
    public $raw_response;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    public function __construct(
        $id,
        $booking_id,
        $transaction_id,
        $payment_method,
        $amount,
        $currency,
        $raw_response,
        $is_active,
        $created_by,
        $updated_by,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->booking_id = $booking_id;
        $this->transaction_id = $transaction_id;
        $this->payment_method = $payment_method;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->raw_response = $raw_response;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(BookingTransaction $transaction): self
    {
        return new self(
            $transaction->id,
            $transaction->booking_id ?? null,
            $transaction->transaction_id ?? null,
            $transaction->payment_method ?? null,
            $transaction->amount ?? null,
            $transaction->currency ?? null,
            $transaction->raw_response ?? null,
            $transaction->is_active ?? true,
            $transaction->created_by ?? null,
            $transaction->updated_by ?? null,
            $transaction->created_at?->toDateTimeString() ?? null,
            $transaction->deleted_at?->toDateTimeString() ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'transaction_id' => $this->transaction_id,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'raw_response' => $this->raw_response,
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
            'booking_id' => $this->booking_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
        ];
    }
}

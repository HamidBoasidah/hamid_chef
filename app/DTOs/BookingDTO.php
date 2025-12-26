<?php

namespace App\DTOs;

use App\Models\Booking;
use Carbon\Carbon;

class BookingDTO extends BaseDTO
{
    public $id;
    public $customer_id;
    public $chef_id;
    public $chef_service_id;
    public $address_id;
    public $date;
    public $start_time;
    public $hours_count;
    public $number_of_guests;
    public $service_type;
    public $unit_price;
    public $extra_guests_count;
    public $extra_guests_amount;
    public $total_amount;
    public $commission_amount;
    public $payment_status;
    public $booking_status;
    public $notes;
    public $is_active;
    public $created_by;
    public $updated_by;
    public $created_at;
    public $deleted_at;

    // Relationships
    public $customer;
    public $chef;
    public $service;
    public $address;

    public function __construct(
        $id,
        $customer_id,
        $chef_id,
        $chef_service_id,
        $address_id,
        $date,
        $start_time,
        $hours_count,
        $number_of_guests,
        $service_type,
        $unit_price,
        $extra_guests_count,
        $extra_guests_amount,
        $total_amount,
        $commission_amount,
        $payment_status,
        $booking_status,
        $notes,
        $is_active,
        $created_by,
        $updated_by,
        $created_at = null,
        $deleted_at = null
    ) {
        $this->id = $id;
        $this->customer_id = $customer_id;
        $this->chef_id = $chef_id;
        $this->chef_service_id = $chef_service_id;
        $this->address_id = $address_id;
        $this->date = $date;
        $this->start_time = $start_time;
        $this->hours_count = $hours_count;
        $this->number_of_guests = $number_of_guests;
        $this->service_type = $service_type;
        $this->unit_price = $unit_price;
        $this->extra_guests_count = $extra_guests_count;
        $this->extra_guests_amount = $extra_guests_amount;
        $this->total_amount = $total_amount;
        $this->commission_amount = $commission_amount;
        $this->payment_status = $payment_status;
        $this->booking_status = $booking_status;
        $this->notes = $notes;
        $this->is_active = (bool) $is_active;
        $this->created_by = $created_by;
        $this->updated_by = $updated_by;
        $this->created_at = $created_at;
        $this->deleted_at = $deleted_at;
    }

    public static function fromModel(Booking $booking): self
    {
        $dto = new self(
            $booking->id,
            $booking->customer_id ?? null,
            $booking->chef_id ?? null,
            $booking->chef_service_id ?? null,
            $booking->address_id ?? null,
            $booking->date?->toDateString() ?? null,
            $booking->start_time ?? null,
            $booking->hours_count ?? null,
            $booking->number_of_guests ?? null,
            $booking->service_type ?? null,
            $booking->unit_price ?? null,
            $booking->extra_guests_count ?? null,
            $booking->extra_guests_amount ?? null,
            $booking->total_amount ?? null,
            $booking->commission_amount ?? null,
            $booking->payment_status ?? null,
            $booking->booking_status ?? null,
            $booking->notes ?? null,
            $booking->is_active ?? true,
            $booking->created_by ?? null,
            $booking->updated_by ?? null,
            $booking->created_at?->toDateTimeString() ?? null,
            $booking->deleted_at?->toDateTimeString() ?? null,
        );

        // Add relationships if loaded
        if ($booking->relationLoaded('customer')) {
            $dto->customer = $booking->customer;
        }
        if ($booking->relationLoaded('chef')) {
            $dto->chef = $booking->chef;
        }
        if ($booking->relationLoaded('service')) {
            $dto->service = $booking->service;
        }
        if ($booking->relationLoaded('address')) {
            $dto->address = $booking->address;
        }

        return $dto;
    }

    public function toArray(): array
    {
        $array = [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'chef_id' => $this->chef_id,
            'chef_service_id' => $this->chef_service_id,
            'address_id' => $this->address_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'hours_count' => $this->hours_count,
            'number_of_guests' => $this->number_of_guests,
            'service_type' => $this->service_type,
            'unit_price' => $this->unit_price,
            'extra_guests_count' => $this->extra_guests_count,
            'extra_guests_amount' => $this->extra_guests_amount,
            'total_amount' => $this->total_amount,
            'commission_amount' => $this->commission_amount,
            'payment_status' => $this->payment_status,
            'booking_status' => $this->booking_status,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'deleted_at' => $this->deleted_at,
        ];

        // Add relationships if they exist
        if (isset($this->customer)) {
            $array['customer'] = $this->customer;
        }
        if (isset($this->chef)) {
            $array['chef'] = $this->chef;
        }
        if (isset($this->service)) {
            $array['service'] = $this->service;
        }
        if (isset($this->address)) {
            $array['address'] = $this->address;
        }

        return $array;
    }

    public function toIndexArray(): array
    {
        $array = [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'chef_id' => $this->chef_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'hours_count' => $this->hours_count,
            'number_of_guests' => $this->number_of_guests,
            'total_amount' => $this->total_amount,
            'booking_status' => $this->booking_status,
        ];

        // Chef details
        if (isset($this->chef)) {
            $array['chef'] = [
                'id' => $this->chef->id ?? null,
                'name' => $this->chef->name ?? null,
                'rating' => $this->chef->rating_avg ?? null,
                'ratings_count' => $this->chef->ratings()->count() ?? 0,
                'image' => $this->chef->logo ?? null,
            ];
        }

        // Service name
        if (isset($this->service)) {
            $array['service_name'] = $this->service->name ?? null;
        }

        return $array;
    }

    // Time calculation methods for conflict detection
    public function getEndTime(): Carbon
    {
        return Carbon::parse($this->start_time)->addHours($this->hours_count);
    }
    
    public function getStartDateTime(): Carbon
    {
        return Carbon::parse($this->date . ' ' . $this->start_time);
    }
    
    public function getEndDateTime(): Carbon
    {
        return $this->getStartDateTime()->addHours($this->hours_count);
    }
    
    // Validation methods
    public function isValidForConflictCheck(): bool
    {
        return !empty($this->chef_id) 
            && !empty($this->date) 
            && !empty($this->start_time) 
            && !empty($this->hours_count)
            && $this->hours_count > 0;
    }
    
    public function isActive(): bool
    {
        return $this->is_active 
            && !in_array($this->booking_status, ['cancelled_by_customer', 'cancelled_by_chef', 'rejected']);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'chef_id',
        'chef_service_id',
        'address_id',
        'date',
        'start_time',
        'hours_count',
        'number_of_guests',
        'service_type',
        'unit_price',
        'extra_guests_count',
        'extra_guests_amount',
        'total_amount',
        'commission_amount',
        'payment_status',
        'booking_status',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'hours_count' => 'integer',
        'number_of_guests' => 'integer',
        'extra_guests_count' => 'integer',
        'unit_price' => 'decimal:2',
        'extra_guests_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'commission_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(ChefService::class, 'chef_service_id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BookingTransaction::class);
    }

    public function rating(): HasOne
    {
        return $this->hasOne(ChefRating::class);
    }
}

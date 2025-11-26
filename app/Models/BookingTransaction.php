<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTransaction extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'payment_method',
        'raw_response',
        'is_active',
        'created_by',
        'updated_by',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

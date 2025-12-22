<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChefServiceRating extends BaseModel
{
    use HasFactory;

    protected $table = 'chef_service_ratings';

    protected $fillable = [
        'booking_id',
        'customer_id',
        'chef_id',
        'rating',
        'review',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }
}

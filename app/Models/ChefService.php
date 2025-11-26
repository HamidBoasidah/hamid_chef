<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChefService extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'chef_id',
        'name',
        'description',
        'service_type',
        'hourly_rate',
        'min_hours',
        'package_price',
        'max_guests_included',
        'allow_extra_guests',
        'extra_guest_price',
        'max_guests_allowed',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'hourly_rate' => 'decimal:2',
        'package_price' => 'decimal:2',
        'extra_guest_price' => 'decimal:2',
        'allow_extra_guests' => 'boolean',
    ];

    public function chef(): BelongsTo
    {
        return $this->belongsTo(Chef::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ChefServiceImage::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'chef_service_tags');
    }

    public function serviceTags(): HasMany
    {
        return $this->hasMany(ChefServiceTag::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chef extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'bio',
        'profile_image',
        'city',
        'area',
        'base_hourly_rate',
        'status',
        'rating_avg',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'base_hourly_rate' => 'decimal:2',
        'rating_avg' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kyc(): HasOne
    {
        return $this->hasOne(Kyc::class, 'user_id', 'user_id');
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(ChefGallery::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ChefService::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'chef_categories', 'chef_id', 'cuisine_id');
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(ChefWallet::class);
    }

    public function walletTransactions(): HasMany
    {
        return $this->hasMany(ChefWalletTransaction::class);
    }

    public function withdrawalRequests(): HasMany
    {
        return $this->hasMany(ChefWithdrawalRequest::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(ChefRating::class);
    }
}

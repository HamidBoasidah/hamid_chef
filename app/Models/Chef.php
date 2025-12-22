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

    protected static function boot()
    {
        parent::boot();

        // يتم تشغيل هذا الحدث عند delete() (soft delete)
        static::deleting(function ($chef) {
            // حذف جميع صور المعرض المرتبطة بالطاهي
            $chef->gallery()->delete();
            
            // حذف جميع التصنيفات المرتبطة بالطاهي من جدول الوسيط
            $chef->categories()->detach();
        });

        // يتم تشغيل هذا الحدث عند forceDelete() (hard delete)
        static::forceDeleting(function ($chef) {
            // حذف جميع صور المعرض نهائياً
            $chef->gallery()->forceDelete();
            
            // حذف جميع التصنيفات المرتبطة بالطاهي من جدول الوسيط
            $chef->categories()->detach();
        });
    }



    protected $fillable = [
        'user_id',
        'name',
        'short_description',
        'long_description',
        'bio',
        'email',
        'phone',
        'logo',
        'banner',
        'address',
        'governorate_id',
        'district_id',
        'area_id',
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
        return $this->belongsTo(User::class, 'user_id');
    }

    public function governorate(): BelongsTo
    {
        return $this->belongsTo(Governorate::class, 'governorate_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function kyc(): HasOne
    {
        // Kyc is linked by user_id; local key on this model is user_id
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
        return $this->belongsToMany(Category::class, 'chef_categories', 'chef_id', 'cuisine_id')
            ->withPivot(['is_active', 'created_by', 'updated_by'])
            ->withTimestamps();
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
        return $this->hasMany(ChefServiceRating::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChefService extends BaseModel
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // إنشاء slug تلقائياً عند الإنشاء
        static::creating(function ($service) {
            if (empty($service->slug) && !empty($service->name)) {
                $service->slug = \Illuminate\Support\Str::slug($service->name);
                
                // التأكد من أن الـ slug فريد
                $originalSlug = $service->slug;
                $counter = 1;
                while (static::where('slug', $service->slug)->exists()) {
                    $service->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        // تحديث slug عند تحديث الاسم
        static::updating(function ($service) {
            if ($service->isDirty('name') && !empty($service->name)) {
                $service->slug = \Illuminate\Support\Str::slug($service->name);
                
                // التأكد من أن الـ slug فريد (باستثناء السجل الحالي)
                $originalSlug = $service->slug;
                $counter = 1;
                while (static::where('slug', $service->slug)->where('id', '!=', $service->id)->exists()) {
                    $service->slug = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }
        });

        // يتم تشغيل هذا الحدث عند delete() (soft delete)
        static::deleting(function ($service) {
            // حذف جميع صور الخدمة المرتبطة
            $service->images()->delete();
            
            // حذف جميع العلامات المرتبطة بالخدمة من جدول الوسيط
            $service->tags()->detach();
        });

        // يتم تشغيل هذا الحدث عند forceDelete() (hard delete)
        static::forceDeleting(function ($service) {
            // حذف جميع صور الخدمة نهائياً
            $service->images()->forceDelete();
            
            // حذف جميع العلامات المرتبطة بالخدمة من جدول الوسيط
            $service->tags()->detach();
        });
    }

    protected $fillable = [
        'chef_id',
        'name',
        'description',
        'feature_image',
        'service_type',
        'hourly_rate',
        'min_hours',
        'package_price',
        'max_guests_included',
        'allow_extra_guests',
        'extra_guest_price',
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

    /**
     * Scope for active services ordered by creation date
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('created_at');
    }

    /**
     * Scope for ordered services by creation date
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('created_at');
    }

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
        return $this->belongsToMany(Tag::class, 'chef_service_tags', 'chef_service_id', 'tag_id')
            ->withPivot(['is_active', 'created_by', 'updated_by'])
            ->withTimestamps();
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

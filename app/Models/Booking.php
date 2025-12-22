<?php

namespace App\Models;

use App\Traits\OptimizedBookingQueries;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends BaseModel
{
    use HasFactory, OptimizedBookingQueries;

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

    // Computed properties for conflict detection
    public function getEndTimeAttribute(): Carbon
    {
        return $this->start_time->copy()->addHours($this->hours_count);
    }
    
    public function getStartDateTimeAttribute(): Carbon
    {
        return Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->start_time->format('H:i:s'));
    }
    
    public function getEndDateTimeAttribute(): Carbon
    {
        return $this->start_date_time->copy()->addHours($this->hours_count);
    }
    
    // Scopes for conflict detection
    public function scopeForChef($query, int $chefId)
    {
        return $query->where('chef_id', $chefId);
    }
    
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->whereNotIn('booking_status', ['cancelled_by_customer', 'cancelled_by_chef', 'rejected']);
    }
    
    public function scopeOnDate($query, Carbon $date)
    {
        return $query->whereDate('date', $date);
    }
    
    public function scopeInDateRange($query, Carbon $startDate, Carbon $endDate)
    {
        return $query->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]);
    }
}

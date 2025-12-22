<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait OptimizedBookingQueries
{
    /**
     * Optimized query for finding conflicting bookings
     */
    public function scopeConflictingWith(Builder $query, int $chefId, Carbon $startDateTime, Carbon $endDateTime, ?int $excludeBookingId = null): Builder
    {
        return $query
            ->select(['id', 'chef_id', 'date', 'start_time', 'hours_count', 'booking_status'])
            ->where('chef_id', $chefId)
            ->where('is_active', true)
            ->whereNotIn('booking_status', ['cancelled_by_customer', 'cancelled_by_chef', 'rejected'])
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                // Use raw SQL for better performance
                $q->whereRaw("
                    (TIMESTAMP(date, start_time) < ? AND 
                     TIMESTAMP(date, TIME(SEC_TO_TIME(TIME_TO_SEC(start_time) + hours_count * 3600))) > ?)
                ", [$endDateTime->format('Y-m-d H:i:s'), $startDateTime->format('Y-m-d H:i:s')]);
            })
            ->when($excludeBookingId, function ($q) use ($excludeBookingId) {
                $q->where('id', '!=', $excludeBookingId);
            })
            ->orderBy('date')
            ->orderBy('start_time');
    }

    /**
     * Optimized query for finding bookings within time range
     */
    public function scopeWithinTimeRange(Builder $query, int $chefId, Carbon $startDateTime, Carbon $endDateTime, ?int $excludeBookingId = null): Builder
    {
        return $query
            ->select(['id', 'chef_id', 'date', 'start_time', 'hours_count', 'booking_status'])
            ->where('chef_id', $chefId)
            ->where('is_active', true)
            ->whereNotIn('booking_status', ['cancelled_by_customer', 'cancelled_by_chef', 'rejected'])
            ->whereBetween('date', [
                $startDateTime->format('Y-m-d'),
                $endDateTime->format('Y-m-d')
            ])
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                $q->whereRaw("
                    (TIMESTAMP(date, start_time) BETWEEN ? AND ?) OR
                    (TIMESTAMP(date, TIME(SEC_TO_TIME(TIME_TO_SEC(start_time) + hours_count * 3600))) BETWEEN ? AND ?) OR
                    (TIMESTAMP(date, start_time) <= ? AND 
                     TIMESTAMP(date, TIME(SEC_TO_TIME(TIME_TO_SEC(start_time) + hours_count * 3600))) >= ?)
                ", [
                    $startDateTime->format('Y-m-d H:i:s'),
                    $endDateTime->format('Y-m-d H:i:s'),
                    $startDateTime->format('Y-m-d H:i:s'),
                    $endDateTime->format('Y-m-d H:i:s'),
                    $startDateTime->format('Y-m-d H:i:s'),
                    $endDateTime->format('Y-m-d H:i:s')
                ]);
            })
            ->when($excludeBookingId, function ($q) use ($excludeBookingId) {
                $q->where('id', '!=', $excludeBookingId);
            })
            ->orderBy('date')
            ->orderBy('start_time');
    }

    /**
     * Optimized query for chef availability check
     */
    public function scopeChefAvailabilityCheck(Builder $query, int $chefId, Carbon $date): Builder
    {
        return $query
            ->select(['id', 'date', 'start_time', 'hours_count'])
            ->where('chef_id', $chefId)
            ->whereDate('date', $date)
            ->where('is_active', true)
            ->whereNotIn('booking_status', ['cancelled_by_customer', 'cancelled_by_chef', 'rejected'])
            ->orderBy('start_time');
    }

    /**
     * Optimized query for admin dashboard
     */
    public function scopeForAdminDashboard(Builder $query, array $filters = []): Builder
    {
        return $query
            ->select([
                'id', 'customer_id', 'chef_id', 'date', 'start_time', 'hours_count',
                'total_amount', 'booking_status', 'payment_status', 'created_at'
            ])
            ->with([
                'customer:id,first_name,last_name,email',
                'chef:id,name'
            ])
            ->when($filters['search'] ?? null, function ($q, $search) {
                $q->where(function ($subQ) use ($search) {
                    $subQ->whereHas('customer', function ($customerQ) use ($search) {
                        $customerQ->where('first_name', 'like', "%{$search}%")
                                 ->orWhere('last_name', 'like', "%{$search}%")
                                 ->orWhere('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('chef', function ($chefQ) use ($search) {
                        $chefQ->where('name', 'like', "%{$search}%");
                    })
                    ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, function ($q, $status) {
                $q->where('booking_status', $status);
            })
            ->when($filters['payment_status'] ?? null, function ($q, $paymentStatus) {
                $q->where('payment_status', $paymentStatus);
            })
            ->when($filters['date_from'] ?? null, function ($q, $dateFrom) {
                $q->whereDate('date', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function ($q, $dateTo) {
                $q->whereDate('date', '<=', $dateTo);
            })
            ->when($filters['chef_id'] ?? null, function ($q, $chefId) {
                $q->where('chef_id', $chefId);
            })
            ->orderBy('created_at', 'desc');
    }

    /**
     * Optimized query for customer bookings
     */
    public function scopeForCustomer(Builder $query, int $customerId, array $filters = []): Builder
    {
        return $query
            ->select([
                'id', 'chef_id', 'chef_service_id', 'date', 'start_time', 'hours_count',
                'total_amount', 'booking_status', 'payment_status', 'created_at'
            ])
            ->with([
                'chef:id,name',
                'service:id,name'
            ])
            ->where('customer_id', $customerId)
            ->when($filters['status'] ?? null, function ($q, $status) {
                $q->where('booking_status', $status);
            })
            ->when($filters['upcoming'] ?? false, function ($q) {
                $q->whereDate('date', '>=', now()->toDateString())
                  ->whereIn('booking_status', ['pending', 'accepted']);
            })
            ->when($filters['past'] ?? false, function ($q) {
                $q->where(function ($subQ) {
                    $subQ->whereDate('date', '<', now()->toDateString())
                         ->orWhere('booking_status', 'completed');
                });
            })
            ->orderBy('date', 'desc')
            ->orderBy('start_time', 'desc');
    }

    /**
     * Optimized query for chef bookings
     */
    public function scopeForChefDashboard(Builder $query, int $chefId, array $filters = []): Builder
    {
        return $query
            ->select([
                'id', 'customer_id', 'chef_service_id', 'date', 'start_time', 'hours_count',
                'number_of_guests', 'total_amount', 'booking_status', 'payment_status', 'notes'
            ])
            ->with([
                'customer:id,first_name,last_name,phone',
                'service:id,name',
                'address:id,address'
            ])
            ->where('chef_id', $chefId)
            ->when($filters['date'] ?? null, function ($q, $date) {
                $q->whereDate('date', $date);
            })
            ->when($filters['status'] ?? null, function ($q, $status) {
                $q->where('booking_status', $status);
            })
            ->when($filters['today'] ?? false, function ($q) {
                $q->whereDate('date', now()->toDateString());
            })
            ->when($filters['upcoming'] ?? false, function ($q) {
                $q->whereDate('date', '>=', now()->toDateString())
                  ->whereIn('booking_status', ['pending', 'accepted']);
            })
            ->orderBy('date')
            ->orderBy('start_time');
    }

    /**
     * Get booking statistics for dashboard
     */
    public function scopeStatistics(Builder $query, array $filters = []): Builder
    {
        return $query
            ->select([
                DB::raw('COUNT(*) as total_bookings'),
                DB::raw('SUM(CASE WHEN booking_status = "pending" THEN 1 ELSE 0 END) as pending_bookings'),
                DB::raw('SUM(CASE WHEN booking_status = "accepted" THEN 1 ELSE 0 END) as accepted_bookings'),
                DB::raw('SUM(CASE WHEN booking_status = "completed" THEN 1 ELSE 0 END) as completed_bookings'),
                DB::raw('SUM(CASE WHEN booking_status IN ("cancelled_by_customer", "cancelled_by_chef", "rejected") THEN 1 ELSE 0 END) as cancelled_bookings'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('AVG(total_amount) as average_booking_value'),
                DB::raw('SUM(commission_amount) as total_commission')
            ])
            ->when($filters['date_from'] ?? null, function ($q, $dateFrom) {
                $q->whereDate('created_at', '>=', $dateFrom);
            })
            ->when($filters['date_to'] ?? null, function ($q, $dateTo) {
                $q->whereDate('created_at', '<=', $dateTo);
            })
            ->when($filters['chef_id'] ?? null, function ($q, $chefId) {
                $q->where('chef_id', $chefId);
            });
    }

    /**
     * Optimized query for conflict detection with locking
     */
    public function scopeLockForConflictCheck(Builder $query, int $chefId, Carbon $date): Builder
    {
        return $query
            ->select(['id', 'chef_id', 'date', 'start_time', 'hours_count'])
            ->where('chef_id', $chefId)
            ->whereDate('date', $date)
            ->where('is_active', true)
            ->whereNotIn('booking_status', ['cancelled_by_customer', 'cancelled_by_chef', 'rejected'])
            ->lockForUpdate();
    }

    /**
     * Get booking density for a chef on a specific date
     */
    public function scopeBookingDensity(Builder $query, int $chefId, Carbon $date): Builder
    {
        return $query
            ->select([
                DB::raw('COUNT(*) as booking_count'),
                DB::raw('SUM(hours_count) as total_hours'),
                DB::raw('MIN(start_time) as earliest_booking'),
                DB::raw('MAX(TIME(SEC_TO_TIME(TIME_TO_SEC(start_time) + hours_count * 3600))) as latest_end_time')
            ])
            ->where('chef_id', $chefId)
            ->whereDate('date', $date)
            ->where('is_active', true)
            ->whereNotIn('booking_status', ['cancelled_by_customer', 'cancelled_by_chef', 'rejected']);
    }
}
<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BookingRepository extends BaseRepository
{
    protected array $defaultWith = [
        'customer',
        'chef',
        'service',
        'address',
        'transactions',
        'rating',
    ];

    public function __construct(Booking $model)
    {
        parent::__construct($model);
    }

    /**
     * Find bookings that conflict with the given time range
     */
    public function findConflictingBookings(
        int $chefId, 
        Carbon $date, 
        Carbon $startDateTime, 
        Carbon $endDateTime, 
        ?int $excludeBookingId = null
    ): Collection {
        $query = $this->model
            ->forChef($chefId)
            ->active()
            ->onDate($date)
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                // Check for overlapping time ranges
                $q->where(function ($subQ) use ($startDateTime, $endDateTime) {
                    // New booking starts during existing booking
                    $subQ->whereRaw("CONCAT(date, ' ', start_time) <= ?", [$startDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("DATE_ADD(CONCAT(date, ' ', start_time), INTERVAL hours_count HOUR) > ?", [$startDateTime->format('Y-m-d H:i:s')]);
                })
                ->orWhere(function ($subQ) use ($startDateTime, $endDateTime) {
                    // New booking ends during existing booking
                    $subQ->whereRaw("CONCAT(date, ' ', start_time) < ?", [$endDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("DATE_ADD(CONCAT(date, ' ', start_time), INTERVAL hours_count HOUR) >= ?", [$endDateTime->format('Y-m-d H:i:s')]);
                })
                ->orWhere(function ($subQ) use ($startDateTime, $endDateTime) {
                    // New booking completely contains existing booking
                    $subQ->whereRaw("CONCAT(date, ' ', start_time) >= ?", [$startDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("DATE_ADD(CONCAT(date, ' ', start_time), INTERVAL hours_count HOUR) <= ?", [$endDateTime->format('Y-m-d H:i:s')]);
                })
                ->orWhere(function ($subQ) use ($startDateTime, $endDateTime) {
                    // Existing booking completely contains new booking
                    $subQ->whereRaw("CONCAT(date, ' ', start_time) <= ?", [$startDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("DATE_ADD(CONCAT(date, ' ', start_time), INTERVAL hours_count HOUR) >= ?", [$endDateTime->format('Y-m-d H:i:s')]);
                });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->get();
    }

    /**
     * Lock chef bookings for a specific date to prevent race conditions
     */
    public function lockChefBookingsForDate(int $chefId, Carbon $date): void
    {
        $this->model
            ->forChef($chefId)
            ->onDate($date)
            ->active()
            ->lockForUpdate()
            ->get();
    }

    /**
     * Find bookings within a specific time range for gap validation
     */
    public function findBookingsWithinTimeRange(
        int $chefId, 
        Carbon $startDateTime, 
        Carbon $endDateTime, 
        ?int $excludeBookingId = null
    ): Collection {
        $query = $this->model
            ->forChef($chefId)
            ->active()
            ->inDateRange($startDateTime, $endDateTime)
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                // Find bookings that are within the search range
                $q->whereRaw("CONCAT(date, ' ', start_time) BETWEEN ? AND ?", [
                    $startDateTime->format('Y-m-d H:i:s'),
                    $endDateTime->format('Y-m-d H:i:s')
                ])
                ->orWhereRaw("DATE_ADD(CONCAT(date, ' ', start_time), INTERVAL hours_count HOUR) BETWEEN ? AND ?", [
                    $startDateTime->format('Y-m-d H:i:s'),
                    $endDateTime->format('Y-m-d H:i:s')
                ])
                ->orWhere(function ($subQ) use ($startDateTime, $endDateTime) {
                    // Bookings that span across the search range
                    $subQ->whereRaw("CONCAT(date, ' ', start_time) <= ?", [$startDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("DATE_ADD(CONCAT(date, ' ', start_time), INTERVAL hours_count HOUR) >= ?", [$endDateTime->format('Y-m-d H:i:s')]);
                });
            });

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->get();
    }

    /**
     * Get chef bookings for a specific date range with optimized query
     */
    public function getChefBookingsInRange(int $chefId, Carbon $startDate, Carbon $endDate): Collection
    {
        return $this->model
            ->forChef($chefId)
            ->active()
            ->inDateRange($startDate, $endDate)
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Check if chef has any active bookings on a specific date
     */
    public function hasActiveBookingsOnDate(int $chefId, Carbon $date): bool
    {
        return $this->model
            ->forChef($chefId)
            ->active()
            ->onDate($date)
            ->exists();
    }
}

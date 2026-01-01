<?php

namespace App\Repositories;

use App\Models\Booking;
use App\Repositories\Eloquent\BaseRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingRepository extends BaseRepository
{
    protected array $defaultWith = [
        'customer',
        'chef',
        'chef.ratings',
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
     * Check if using SQLite database
     */
    protected function isSqlite(): bool
    {
        return DB::connection()->getDriverName() === 'sqlite';
    }

    /**
     * Get database-agnostic datetime concatenation expression
     */
    protected function getDateTimeConcat(): string
    {
        if ($this->isSqlite()) {
            return "datetime(date || ' ' || start_time)";
        }
        return "CONCAT(date, ' ', start_time)";
    }

    /**
     * Get database-agnostic datetime addition expression
     */
    protected function getDateTimeAddHours(string $baseExpr, string $hoursColumn): string
    {
        if ($this->isSqlite()) {
            return "datetime({$baseExpr}, '+' || {$hoursColumn} || ' hours')";
        }
        return "DATE_ADD({$baseExpr}, INTERVAL {$hoursColumn} HOUR)";
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
        $dateTimeConcat = $this->getDateTimeConcat();
        $dateTimeAddHours = $this->getDateTimeAddHours($dateTimeConcat, 'hours_count');

        $query = $this->model
            ->forChef($chefId)
            ->active()
            ->onDate($date)
            ->where(function ($q) use ($startDateTime, $endDateTime, $dateTimeConcat, $dateTimeAddHours) {
                // Check for overlapping time ranges
                $q->where(function ($subQ) use ($startDateTime, $endDateTime, $dateTimeConcat, $dateTimeAddHours) {
                    // New booking starts during existing booking
                    $subQ->whereRaw("{$dateTimeConcat} <= ?", [$startDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("{$dateTimeAddHours} > ?", [$startDateTime->format('Y-m-d H:i:s')]);
                })
                ->orWhere(function ($subQ) use ($startDateTime, $endDateTime, $dateTimeConcat, $dateTimeAddHours) {
                    // New booking ends during existing booking
                    $subQ->whereRaw("{$dateTimeConcat} < ?", [$endDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("{$dateTimeAddHours} >= ?", [$endDateTime->format('Y-m-d H:i:s')]);
                })
                ->orWhere(function ($subQ) use ($startDateTime, $endDateTime, $dateTimeConcat, $dateTimeAddHours) {
                    // New booking completely contains existing booking
                    $subQ->whereRaw("{$dateTimeConcat} >= ?", [$startDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("{$dateTimeAddHours} <= ?", [$endDateTime->format('Y-m-d H:i:s')]);
                })
                ->orWhere(function ($subQ) use ($startDateTime, $endDateTime, $dateTimeConcat, $dateTimeAddHours) {
                    // Existing booking completely contains new booking
                    $subQ->whereRaw("{$dateTimeConcat} <= ?", [$startDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("{$dateTimeAddHours} >= ?", [$endDateTime->format('Y-m-d H:i:s')]);
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
        $dateTimeConcat = $this->getDateTimeConcat();
        $dateTimeAddHours = $this->getDateTimeAddHours($dateTimeConcat, 'hours_count');

        $query = $this->model
            ->forChef($chefId)
            ->active()
            ->inDateRange($startDateTime, $endDateTime)
            ->where(function ($q) use ($startDateTime, $endDateTime, $dateTimeConcat, $dateTimeAddHours) {
                // Find bookings that are within the search range
                $q->whereRaw("{$dateTimeConcat} BETWEEN ? AND ?", [
                    $startDateTime->format('Y-m-d H:i:s'),
                    $endDateTime->format('Y-m-d H:i:s')
                ])
                ->orWhereRaw("{$dateTimeAddHours} BETWEEN ? AND ?", [
                    $startDateTime->format('Y-m-d H:i:s'),
                    $endDateTime->format('Y-m-d H:i:s')
                ])
                ->orWhere(function ($subQ) use ($startDateTime, $endDateTime, $dateTimeConcat, $dateTimeAddHours) {
                    // Bookings that span across the search range
                    $subQ->whereRaw("{$dateTimeConcat} <= ?", [$startDateTime->format('Y-m-d H:i:s')])
                         ->whereRaw("{$dateTimeAddHours} >= ?", [$endDateTime->format('Y-m-d H:i:s')]);
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

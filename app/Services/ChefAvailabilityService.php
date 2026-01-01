<?php

namespace App\Services;

use App\Models\Chef;
use App\Models\Booking;
use App\Models\ChefWorkingHour;
use App\Models\ChefService;
use App\Repositories\BookingRepository;
use App\Repositories\ChefWorkingHourRepository;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class ChefAvailabilityService
{
    protected BookingRepository $bookingRepository;
    protected ChefWorkingHourRepository $workingHourRepository;
    protected int $defaultRestHours;

    public function __construct(
        BookingRepository $bookingRepository,
        ChefWorkingHourRepository $workingHourRepository
    ) {
        $this->bookingRepository = $bookingRepository;
        $this->workingHourRepository = $workingHourRepository;
        $this->defaultRestHours = config('booking.minimum_gap_hours', 2);
    }

    /**
     * Get comprehensive availability data for a chef
     * 
     * Calendar logic:
     * - From the provided date (or today) until end of month
     * - If remaining days in month < 10, go back to ensure at least 10 days
     * 
     * @param int $chefId
     * @param string|null $date Target date for calendar (defaults to today)
     * @return array
     */
    public function getChefAvailability(int $chefId, ?string $date = null): array
    {
        $chef = Chef::with(['services' => function($q) {
            $q->where('is_active', true);
        }])->findOrFail($chefId);

        // التاريخ المرسل أو اليوم كـ default
        $targetDate = $date ? Carbon::parse($date) : Carbon::today();
        
        // Calculate calendar range: from target date to end of month
        // If remaining days < 10, go back to ensure at least 10 days
        [$startDate, $endDate] = $this->calculateCalendarRange($targetDate);

        // Get working hours for the chef
        $workingHours = $this->getWorkingHoursMap($chefId);

        // Get all bookings in the date range (with service for rest_hours)
        $bookings = $this->getBookingsInRange($chefId, $startDate, $endDate);

        // Build calendar data
        $calendar = $this->buildCalendar($startDate, $endDate, $workingHours, $bookings);

        // Get detailed day info for the target date
        $dayDetails = $this->getDayDetails($chefId, $targetDate, $workingHours, $bookings);

        return [
            'chef_id' => $chefId,
            'chef_name' => $chef->name,
            'default_rest_hours' => $this->defaultRestHours,
            'calendar_start_date' => $startDate->format('Y-m-d'),
            'calendar_end_date' => $endDate->format('Y-m-d'),
            'calendar' => $calendar,
            'selected_date' => $targetDate->format('Y-m-d'),
            'day_details' => $dayDetails,
            'services' => $chef->services->map(function($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'service_type' => $service->service_type,
                    'hourly_rate' => $service->hourly_rate,
                    'min_hours' => $service->min_hours,
                    'package_price' => $service->package_price,
                    'rest_hours_required' => $service->rest_hours_required ?? $this->defaultRestHours,
                ];
            }),
        ];
    }

    /**
     * Calculate calendar date range
     * 
     * Logic:
     * - End date is always end of month
     * - Start date is the target date
     * - If remaining days in month < 10, go back to ensure at least 10 days
     * 
     * Examples:
     * - Date: Jan 15 → Start: Jan 15, End: Jan 31 (17 days)
     * - Date: Jan 25 → Start: Jan 22, End: Jan 31 (10 days, went back 3 days)
     * - Date: Jan 30 → Start: Jan 21, End: Jan 31 (11 days including 30 & 31)
     * 
     * @param Carbon $targetDate
     * @return array [Carbon $startDate, Carbon $endDate]
     */
    protected function calculateCalendarRange(Carbon $targetDate): array
    {
        $endDate = $targetDate->copy()->endOfMonth()->startOfDay();
        
        // Calculate remaining days from target date to end of month (inclusive)
        $remainingDays = $targetDate->diffInDays($endDate) + 1; // +1 to include both start and end
        
        // Minimum 10 days in calendar
        $minDays = 10;
        
        if ($remainingDays >= $minDays) {
            // Enough days remaining, start from target date
            $startDate = $targetDate->copy();
        } else {
            // Not enough days, go back to ensure at least 10 days
            $daysToGoBack = $minDays - $remainingDays;
            $startDate = $targetDate->copy()->subDays($daysToGoBack);
        }
        
        return [$startDate, $endDate];
    }

    /**
     * Get working hours mapped by day of week
     */
    protected function getWorkingHoursMap(int $chefId): array
    {
        $workingHours = $this->workingHourRepository
            ->forChef($chefId)
            ->where('is_active', true)
            ->get();

        $map = [];
        foreach ($workingHours as $wh) {
            if (!isset($map[$wh->day_of_week])) {
                $map[$wh->day_of_week] = [];
            }
            $map[$wh->day_of_week][] = [
                'start_time' => $wh->start_time,
                'end_time' => $wh->end_time,
            ];
        }

        return $map;
    }

    /**
     * Get bookings in a date range
     */
    protected function getBookingsInRange(int $chefId, Carbon $startDate, Carbon $endDate): Collection
    {
        return Booking::where('chef_id', $chefId)
            ->where('is_active', true)
            ->whereNotIn('booking_status', ['cancelled_by_customer', 'cancelled_by_chef', 'rejected'])
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with(['service:id,name,service_type,min_hours,rest_hours_required'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
    }

    /**
     * Build calendar with availability status for each day
     */
    protected function buildCalendar(Carbon $startDate, Carbon $endDate, array $workingHours, Collection $bookings): array
    {
        $calendar = [
            'available_days' => [],
            'off_days' => [],
            'partially_booked_days' => [],
            'fully_booked_days' => [],
        ];

        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $day) {
            $dayOfWeek = $day->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $dateStr = $day->format('Y-m-d');

            // Check if chef works on this day
            if (!isset($workingHours[$dayOfWeek]) || empty($workingHours[$dayOfWeek])) {
                $calendar['off_days'][] = [
                    'date' => $dateStr,
                    'day_name' => $day->translatedFormat('l'),
                    'day_name_ar' => $this->getArabicDayName($dayOfWeek),
                ];
                continue;
            }

            // Get bookings for this day
            $dayBookings = $bookings->filter(function($booking) use ($dateStr) {
                return $booking->date->format('Y-m-d') === $dateStr;
            });

            // Calculate availability status
            $workingSlots = $workingHours[$dayOfWeek];
            $totalWorkingMinutes = $this->calculateTotalWorkingMinutes($workingSlots);
            $bookedMinutes = $this->calculateBookedMinutesWithServiceRest($dayBookings);

            $availabilityPercentage = $totalWorkingMinutes > 0 
                ? (($totalWorkingMinutes - $bookedMinutes) / $totalWorkingMinutes) * 100 
                : 0;

            // Ensure percentage is not negative
            $availabilityPercentage = max(0, $availabilityPercentage);

            $dayInfo = [
                'date' => $dateStr,
                'day_name' => $day->translatedFormat('l'),
                'day_name_ar' => $this->getArabicDayName($dayOfWeek),
                'bookings_count' => $dayBookings->count(),
                'availability_percentage' => round($availabilityPercentage, 1),
            ];

            if ($dayBookings->isEmpty()) {
                $calendar['available_days'][] = $dayInfo;
            } elseif ($availabilityPercentage <= 10) {
                $calendar['fully_booked_days'][] = $dayInfo;
            } else {
                $calendar['partially_booked_days'][] = $dayInfo;
            }
        }

        return $calendar;
    }

    /**
     * Get detailed information for a specific day
     */
    protected function getDayDetails(int $chefId, Carbon $date, array $workingHours, Collection $allBookings): array
    {
        $dayOfWeek = $date->dayOfWeek;
        $dateStr = $date->format('Y-m-d');

        // Check if it's an off day
        if (!isset($workingHours[$dayOfWeek]) || empty($workingHours[$dayOfWeek])) {
            return [
                'date' => $dateStr,
                'day_name' => $date->translatedFormat('l'),
                'day_name_ar' => $this->getArabicDayName($dayOfWeek),
                'is_working_day' => false,
                'is_off_day' => true,
                'working_hours' => [],
                'bookings' => [],
                'available_slots' => [],
            ];
        }

        // Get working hours for this day
        $dayWorkingHours = $workingHours[$dayOfWeek];

        // Get bookings for this day
        $dayBookings = $allBookings->filter(function($booking) use ($dateStr) {
            return $booking->date->format('Y-m-d') === $dateStr;
        })->values();

        // Format bookings with service details and rest hours
        $formattedBookings = $dayBookings->map(function($booking) {
            $startTime = Carbon::parse($booking->start_time);
            $endTime = $startTime->copy()->addHours($booking->hours_count);
            
            // Get rest hours from service or use default
            $restHours = $booking->service->rest_hours_required ?? $this->defaultRestHours;
            
            // Calculate blocked time including rest period
            $blockedEndTime = $endTime->copy()->addHours($restHours);

            return [
                'id' => $booking->id,
                'start_time' => $startTime->format('H:i'),
                'end_time' => $endTime->format('H:i'),
                'hours_count' => $booking->hours_count,
                'rest_hours_required' => $restHours,
                'blocked_until' => $blockedEndTime->format('H:i'),
                'total_blocked_hours' => $booking->hours_count + $restHours,
                'booking_status' => $booking->booking_status,
                'service' => $booking->service ? [
                    'id' => $booking->service->id,
                    'name' => $booking->service->name,
                    'service_type' => $booking->service->service_type,
                    'min_hours' => $booking->service->min_hours,
                    'rest_hours_required' => $booking->service->rest_hours_required ?? $this->defaultRestHours,
                ] : null,
            ];
        });

        // Calculate available time slots
        $availableSlots = $this->calculateAvailableSlots($dayWorkingHours, $dayBookings, $date);

        return [
            'date' => $dateStr,
            'day_name' => $date->translatedFormat('l'),
            'day_name_ar' => $this->getArabicDayName($dayOfWeek),
            'is_working_day' => true,
            'is_off_day' => false,
            'working_hours' => array_map(function($wh) {
                return [
                    'start_time' => $wh['start_time'],
                    'end_time' => $wh['end_time'],
                ];
            }, $dayWorkingHours),
            'bookings' => $formattedBookings->toArray(),
            'available_slots' => $availableSlots,
            'default_rest_hours' => $this->defaultRestHours,
        ];
    }

    /**
     * Calculate available time slots for a day
     */
    protected function calculateAvailableSlots(array $workingHours, Collection $bookings, Carbon $date): array
    {
        $availableSlots = [];

        foreach ($workingHours as $shift) {
            $shiftStart = Carbon::parse($date->format('Y-m-d') . ' ' . $shift['start_time']);
            $shiftEnd = Carbon::parse($date->format('Y-m-d') . ' ' . $shift['end_time']);

            // Get bookings that overlap with this shift
            $shiftBookings = $bookings->filter(function($booking) use ($shiftStart, $shiftEnd) {
                $bookingStart = Carbon::parse($booking->date->format('Y-m-d') . ' ' . $booking->start_time->format('H:i'));
                $bookingEnd = $bookingStart->copy()->addHours($booking->hours_count);
                
                return $bookingStart < $shiftEnd && $bookingEnd > $shiftStart;
            })->sortBy(function($booking) {
                return $booking->start_time->format('H:i');
            });

            $currentTime = $shiftStart->copy();

            foreach ($shiftBookings as $booking) {
                $bookingStart = Carbon::parse($date->format('Y-m-d') . ' ' . $booking->start_time->format('H:i'));
                $bookingEnd = $bookingStart->copy()->addHours($booking->hours_count);
                
                // Get rest hours from service
                $restHours = $booking->service->rest_hours_required ?? $this->defaultRestHours;
                $blockedUntil = $bookingEnd->copy()->addHours($restHours);

                // Add available slot before this booking
                if ($currentTime < $bookingStart) {
                    $slotDuration = $currentTime->diffInMinutes($bookingStart);
                    if ($slotDuration >= 60) { // At least 1 hour
                        $availableSlots[] = [
                            'start_time' => $currentTime->format('H:i'),
                            'end_time' => $bookingStart->format('H:i'),
                            'duration_hours' => round($slotDuration / 60, 1),
                        ];
                    }
                }

                // Move current time to after the blocked period
                $currentTime = $blockedUntil > $currentTime ? $blockedUntil : $currentTime;
            }

            // Add remaining time after last booking
            if ($currentTime < $shiftEnd) {
                $slotDuration = $currentTime->diffInMinutes($shiftEnd);
                if ($slotDuration >= 60) { // At least 1 hour
                    $availableSlots[] = [
                        'start_time' => $currentTime->format('H:i'),
                        'end_time' => $shiftEnd->format('H:i'),
                        'duration_hours' => round($slotDuration / 60, 1),
                    ];
                }
            }
        }

        return $availableSlots;
    }

    /**
     * Calculate total working minutes from shifts
     */
    protected function calculateTotalWorkingMinutes(array $shifts): int
    {
        $total = 0;
        foreach ($shifts as $shift) {
            $start = Carbon::parse($shift['start_time']);
            $end = Carbon::parse($shift['end_time']);
            $total += $start->diffInMinutes($end);
        }
        return $total;
    }

    /**
     * Calculate booked minutes from bookings (using service-specific rest hours)
     */
    protected function calculateBookedMinutesWithServiceRest(Collection $bookings): int
    {
        return $bookings->sum(function($booking) {
            // Get rest hours from service or use default
            $restHours = $booking->service->rest_hours_required ?? $this->defaultRestHours;
            // Total blocked time = booking hours + rest hours
            return ($booking->hours_count + $restHours) * 60;
        });
    }

    /**
     * Get Arabic day name
     */
    protected function getArabicDayName(int $dayOfWeek): string
    {
        $days = [
            0 => 'الأحد',
            1 => 'الإثنين',
            2 => 'الثلاثاء',
            3 => 'الأربعاء',
            4 => 'الخميس',
            5 => 'الجمعة',
            6 => 'السبت',
        ];

        return $days[$dayOfWeek] ?? '';
    }
}

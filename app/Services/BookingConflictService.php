<?php

namespace App\Services;

use App\DTOs\BookingDTO;
use App\Exceptions\BookingConflictException;
use App\Exceptions\BookingValidationException;
use App\Models\Booking;
use App\Models\ChefService;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingConflictService
{
    protected BookingRepository $bookingRepository;
    protected int $defaultRestHours;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->defaultRestHours = config('booking.minimum_gap_hours', 2);
    }

    /**
     * Get rest hours required for a service
     */
    protected function getServiceRestHours(?int $serviceId): int
    {
        if (!$serviceId) {
            return $this->defaultRestHours;
        }

        $service = ChefService::find($serviceId);
        return $service?->rest_hours_required ?? $this->defaultRestHours;
    }

    /**
     * Validate if a booking conflicts with existing bookings
     */
    public function validateBookingConflicts(BookingDTO $booking, ?int $excludeBookingId = null): array
    {
        if (!$booking->isValidForConflictCheck()) {
            return [
                'valid' => false,
                'errors' => ['Invalid booking data for conflict check'],
                'conflicting_bookings' => []
            ];
        }

        $conflictingBookings = $this->findConflictingBookings($booking, $excludeBookingId);
        
        if ($conflictingBookings->isEmpty()) {
            return [
                'valid' => true,
                'errors' => [],
                'conflicting_bookings' => []
            ];
        }

        return [
            'valid' => false,
            'errors' => ['Booking conflicts with existing reservations'],
            'conflicting_bookings' => $conflictingBookings->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'date' => $booking->date->format('Y-m-d'),
                    'start_time' => $booking->start_time->format('H:i'),
                    'end_time' => $booking->end_time->format('H:i'),
                    'hours_count' => $booking->hours_count
                ];
            })->toArray()
        ];
    }

    /**
     * Validate if booking respects minimum time gaps (using service-specific rest hours)
     */
    public function validateTimeGaps(BookingDTO $booking, ?int $excludeBookingId = null): array
    {
        if (!$booking->isValidForConflictCheck()) {
            return [
                'valid' => false,
                'errors' => ['Invalid booking data for gap validation'],
                'conflicting_bookings' => []
            ];
        }

        // Get rest hours for the new booking's service
        $newBookingRestHours = $this->getServiceRestHours($booking->chef_service_id);

        $nearbyBookings = $this->findNearbyBookings($booking, $excludeBookingId, $newBookingRestHours);
        $gapViolations = [];

        foreach ($nearbyBookings as $existingBooking) {
            $gapViolation = $this->checkTimeGapViolation($booking, $existingBooking, $newBookingRestHours);
            if ($gapViolation) {
                $gapViolations[] = $gapViolation;
            }
        }

        if (empty($gapViolations)) {
            return [
                'valid' => true,
                'errors' => [],
                'conflicting_bookings' => []
            ];
        }

        return [
            'valid' => false,
            'errors' => ["Rest hours requirement not met between bookings"],
            'conflicting_bookings' => $gapViolations
        ];
    }

    /**
     * Create booking with database locking to prevent race conditions
     */
    public function createBookingWithLocking(BookingDTO $bookingDTO): array
    {
        return DB::transaction(function () use ($bookingDTO) {
            // Lock chef bookings for the date to prevent race conditions
            $this->bookingRepository->lockChefBookingsForDate(
                $bookingDTO->chef_id, 
                Carbon::parse($bookingDTO->date)
            );

            // Re-validate conflicts after acquiring lock
            $conflictValidation = $this->validateBookingConflicts($bookingDTO);
            if (!$conflictValidation['valid']) {
                return [
                    'success' => false,
                    'message' => 'Booking conflicts detected',
                    'errors' => $conflictValidation['errors'],
                    'conflicting_bookings' => $conflictValidation['conflicting_bookings']
                ];
            }

            // Re-validate time gaps after acquiring lock
            $gapValidation = $this->validateTimeGaps($bookingDTO);
            if (!$gapValidation['valid']) {
                return [
                    'success' => false,
                    'message' => 'Time gap requirements not met',
                    'errors' => $gapValidation['errors'],
                    'conflicting_bookings' => $gapValidation['conflicting_bookings']
                ];
            }

            // Create the booking
            $booking = $this->bookingRepository->create($bookingDTO->toArray());

            // booking created successfully (logging removed)

            return [
                'success' => true,
                'message' => 'Booking created successfully',
                // Return the Eloquent Booking model; controllers/services will convert to DTO
                'booking' => $booking,
                'errors' => []
            ];
        });
    }

    /**
     * Find bookings that overlap with the given booking
     */
    protected function findConflictingBookings(BookingDTO $booking, ?int $excludeBookingId = null): Collection
    {
        $startDateTime = $booking->getStartDateTime();
        $endDateTime = $booking->getEndDateTime();

        return $this->bookingRepository->findConflictingBookings(
            $booking->chef_id,
            Carbon::parse($booking->date),
            $startDateTime,
            $endDateTime,
            $excludeBookingId
        );
    }

    /**
     * Find bookings near the given booking for gap validation
     * Uses the maximum of new booking's rest hours and existing bookings' rest hours
     */
    protected function findNearbyBookings(BookingDTO $booking, ?int $excludeBookingId = null, ?int $newBookingRestHours = null): Collection
    {
        $startDateTime = $booking->getStartDateTime();
        $endDateTime = $booking->getEndDateTime();
        
        // Use the provided rest hours or get from service
        $restHours = $newBookingRestHours ?? $this->getServiceRestHours($booking->chef_service_id);
        
        // Extend search range by maximum possible rest hours (use a safe maximum)
        $maxRestHours = max($restHours, 8); // Search up to 8 hours to catch all potential conflicts
        $searchStart = $startDateTime->copy()->subHours($maxRestHours);
        $searchEnd = $endDateTime->copy()->addHours($maxRestHours);

        return $this->bookingRepository->findBookingsWithinTimeRange(
            $booking->chef_id,
            $searchStart,
            $searchEnd,
            $excludeBookingId
        );
    }

    /**
     * Check if two bookings violate the rest hours requirement
     * Uses service-specific rest hours for each booking
     */
    protected function checkTimeGapViolation(BookingDTO $newBooking, Booking $existingBooking, ?int $newBookingRestHours = null): ?array
    {
        $newStart = $newBooking->getStartDateTime();
        $newEnd = $newBooking->getEndDateTime();
        $existingStart = $existingBooking->start_date_time;
        $existingEnd = $existingBooking->end_date_time;

        // Get rest hours for both bookings
        $newRestHours = $newBookingRestHours ?? $this->getServiceRestHours($newBooking->chef_service_id);
        $existingRestHours = $existingBooking->service?->rest_hours_required ?? $this->defaultRestHours;

        // Check gap before existing booking (new booking ends before existing starts)
        // The new booking needs its rest hours before the existing booking can start
        if ($newEnd->lte($existingStart)) {
            // Gap = time between new booking end and existing booking start
            $gap = $newEnd->diffInHours($existingStart);
            // Gap must be >= rest hours (booking can start exactly when rest period ends)
            if ($gap < $newRestHours) {
                return [
                    'id' => $existingBooking->id,
                    'date' => $existingBooking->date->format('Y-m-d'),
                    'start_time' => $existingBooking->start_time->format('H:i'),
                    'end_time' => $existingBooking->end_time->format('H:i'),
                    'gap_hours' => $gap,
                    'required_rest_hours' => $newRestHours,
                    'violation_type' => 'insufficient_rest_after_new_booking',
                    'service_name' => $existingBooking->service?->name
                ];
            }
        }

        // Check gap after existing booking (new booking starts after existing ends)
        // The existing booking needs its rest hours before the new booking can start
        if ($newStart->gte($existingEnd)) {
            // Gap = time between existing booking end and new booking start
            $gap = $existingEnd->diffInHours($newStart);
            // Gap must be >= rest hours (booking can start exactly when rest period ends)
            if ($gap < $existingRestHours) {
                return [
                    'id' => $existingBooking->id,
                    'date' => $existingBooking->date->format('Y-m-d'),
                    'start_time' => $existingBooking->start_time->format('H:i'),
                    'end_time' => $existingBooking->end_time->format('H:i'),
                    'gap_hours' => $gap,
                    'required_rest_hours' => $existingRestHours,
                    'violation_type' => 'insufficient_rest_after_existing_booking',
                    'service_name' => $existingBooking->service?->name
                ];
            }
        }

        return null;
    }

    /**
     * Check if a chef is available for a specific time slot
     */
    public function checkChefAvailability(int $chefId, string $date, string $startTime, int $hoursCount, ?int $serviceId = null, ?int $excludeBookingId = null): array
    {
        $bookingDTO = new BookingDTO(
            null, null, $chefId, $serviceId, null, $date, $startTime, $hoursCount,
            null, null, null, null, null, null, null, null, null, null, true, null, null
        );

        $conflictValidation = $this->validateBookingConflicts($bookingDTO, $excludeBookingId);
        $gapValidation = $this->validateTimeGaps($bookingDTO, $excludeBookingId);

        $available = $conflictValidation['valid'] && $gapValidation['valid'];
        $errors = array_merge($conflictValidation['errors'], $gapValidation['errors']);
        $conflictingBookings = array_merge(
            $conflictValidation['conflicting_bookings'], 
            $gapValidation['conflicting_bookings']
        );

        return [
            'available' => $available,
            'errors' => $errors,
            'conflicting_bookings' => $conflictingBookings
        ];
    }
}
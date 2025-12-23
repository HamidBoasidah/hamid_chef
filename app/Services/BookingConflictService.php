<?php

namespace App\Services;

use App\DTOs\BookingDTO;
use App\Exceptions\BookingConflictException;
use App\Exceptions\BookingValidationException;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingConflictService
{
    protected BookingRepository $bookingRepository;
    protected int $minimumGapHours;

    public function __construct(BookingRepository $bookingRepository)
    {
        $this->bookingRepository = $bookingRepository;
        $this->minimumGapHours = config('booking.minimum_gap_hours', 2);
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
     * Validate if booking respects minimum time gaps
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

        $nearbyBookings = $this->findNearbyBookings($booking, $excludeBookingId);
        $gapViolations = [];

        foreach ($nearbyBookings as $existingBooking) {
            $gapViolation = $this->checkTimeGapViolation($booking, $existingBooking);
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
            'errors' => ["Minimum {$this->minimumGapHours}-hour gap required between bookings"],
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
                'booking' => BookingDTO::fromModel($booking),
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
     */
    protected function findNearbyBookings(BookingDTO $booking, ?int $excludeBookingId = null): Collection
    {
        $startDateTime = $booking->getStartDateTime();
        $endDateTime = $booking->getEndDateTime();
        
        // Extend search range by minimum gap hours
        $searchStart = $startDateTime->copy()->subHours($this->minimumGapHours);
        $searchEnd = $endDateTime->copy()->addHours($this->minimumGapHours);

        return $this->bookingRepository->findBookingsWithinTimeRange(
            $booking->chef_id,
            $searchStart,
            $searchEnd,
            $excludeBookingId
        );
    }

    /**
     * Check if two bookings violate the minimum time gap
     */
    protected function checkTimeGapViolation(BookingDTO $newBooking, Booking $existingBooking): ?array
    {
        $newStart = $newBooking->getStartDateTime();
        $newEnd = $newBooking->getEndDateTime();
        $existingStart = $existingBooking->start_date_time;
        $existingEnd = $existingBooking->end_date_time;

        // Check gap before existing booking
        if ($newEnd->lte($existingStart)) {
            $gap = $existingStart->diffInHours($newEnd);
            if ($gap < $this->minimumGapHours) {
                return [
                    'id' => $existingBooking->id,
                    'date' => $existingBooking->date->format('Y-m-d'),
                    'start_time' => $existingBooking->start_time->format('H:i'),
                    'end_time' => $existingBooking->end_time->format('H:i'),
                    'gap_hours' => $gap,
                    'required_gap' => $this->minimumGapHours,
                    'violation_type' => 'insufficient_gap_before'
                ];
            }
        }

        // Check gap after existing booking
        if ($newStart->gte($existingEnd)) {
            $gap = $newStart->diffInHours($existingEnd);
            if ($gap < $this->minimumGapHours) {
                return [
                    'id' => $existingBooking->id,
                    'date' => $existingBooking->date->format('Y-m-d'),
                    'start_time' => $existingBooking->start_time->format('H:i'),
                    'end_time' => $existingBooking->end_time->format('H:i'),
                    'gap_hours' => $gap,
                    'required_gap' => $this->minimumGapHours,
                    'violation_type' => 'insufficient_gap_after'
                ];
            }
        }

        return null;
    }

    /**
     * Check if a chef is available for a specific time slot
     */
    public function checkChefAvailability(int $chefId, string $date, string $startTime, int $hoursCount, ?int $excludeBookingId = null): array
    {
        $bookingDTO = new BookingDTO(
            null, null, $chefId, null, null, $date, $startTime, $hoursCount,
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
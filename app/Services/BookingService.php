<?php

namespace App\Services;

use App\DTOs\BookingDTO;
use App\Repositories\BookingRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BookingService
{
    protected BookingRepository $bookings;
    protected BookingConflictService $conflictService;

    public function __construct(BookingRepository $bookings, BookingConflictService $conflictService)
    {
        $this->bookings = $bookings;
        $this->conflictService = $conflictService;
    }

    public function all(array $with = [])
    {
        return $this->bookings->all($with);
    }

    public function paginate(int $perPage = 15, array $with = [])
    {
        return $this->bookings->paginate($perPage, $with);
    }

    /**
     * Paginate bookings belonging to a specific user (customer).
     */
    public function paginateForUser(int $userId, int $perPage = 15, array $with = [])
    {
        // Bookings table uses customer_id rather than user_id — build a query
        $query = $this->bookings->query($with)->where('customer_id', $userId);
        return $query->latest()->paginate($perPage);
    }

    /**
     * Paginate bookings for a specific chef (chef_id).
     */
    public function paginateForChef(int $chefId, int $perPage = 15, array $with = [])
    {
        $query = $this->bookings->query($with)->forChef($chefId);
        return $query->latest()->paginate($perPage);
    }

    public function find($id, array $with = [])
    {
        return $this->bookings->findOrFail($id, $with);
    }

    public function create(array $attributes)
    {
        // Convert to DTO for validation
        $bookingDTO = new BookingDTO(
            null,
            $attributes['customer_id'] ?? null,
            $attributes['chef_id'] ?? null,
            $attributes['chef_service_id'] ?? null,
            $attributes['address_id'] ?? null,
            $attributes['date'] ?? null,
            $attributes['start_time'] ?? null,
            $attributes['hours_count'] ?? null,
            $attributes['number_of_guests'] ?? null,
            $attributes['service_type'] ?? null,
            $attributes['unit_price'] ?? null,
            $attributes['extra_guests_count'] ?? null,
            $attributes['extra_guests_amount'] ?? null,
            $attributes['total_amount'] ?? null,
            $attributes['commission_amount'] ?? null,
            $attributes['payment_status'] ?? 'pending',
            $attributes['booking_status'] ?? 'pending',
            $attributes['notes'] ?? null,
            $attributes['is_active'] ?? true,
            $attributes['created_by'] ?? null,
            $attributes['updated_by'] ?? null
        );

        // Use conflict service for safe creation
        $result = $this->conflictService->createBookingWithLocking($bookingDTO);
        
        if (!$result['success']) {
            throw new \Exception($result['message'], 409);
        }

        return $result['booking'];
    }

    public function createWithConflictCheck(BookingDTO $bookingDTO): array
    {
        return $this->conflictService->createBookingWithLocking($bookingDTO);
    }

    public function update($id, array $attributes)
    {
        $existingBooking = $this->bookings->findOrFail($id);
        
        // Check if time-related fields are being updated
        $timeFieldsChanged = isset($attributes['date']) || 
                           isset($attributes['start_time']) || 
                           isset($attributes['hours_count']);

        if ($timeFieldsChanged) {
            // Create DTO with updated values for conflict validation
            $bookingDTO = new BookingDTO(
                $id,
                $attributes['customer_id'] ?? $existingBooking->customer_id,
                $attributes['chef_id'] ?? $existingBooking->chef_id,
                $attributes['chef_service_id'] ?? $existingBooking->chef_service_id,
                $attributes['address_id'] ?? $existingBooking->address_id,
                $attributes['date'] ?? $existingBooking->date->format('Y-m-d'),
                $attributes['start_time'] ?? $existingBooking->start_time->format('H:i'),
                $attributes['hours_count'] ?? $existingBooking->hours_count,
                $attributes['number_of_guests'] ?? $existingBooking->number_of_guests,
                $attributes['service_type'] ?? $existingBooking->service_type,
                $attributes['unit_price'] ?? $existingBooking->unit_price,
                $attributes['extra_guests_count'] ?? $existingBooking->extra_guests_count,
                $attributes['extra_guests_amount'] ?? $existingBooking->extra_guests_amount,
                $attributes['total_amount'] ?? $existingBooking->total_amount,
                $attributes['commission_amount'] ?? $existingBooking->commission_amount,
                $attributes['payment_status'] ?? $existingBooking->payment_status,
                $attributes['booking_status'] ?? $existingBooking->booking_status,
                $attributes['notes'] ?? $existingBooking->notes,
                $attributes['is_active'] ?? $existingBooking->is_active,
                $attributes['created_by'] ?? $existingBooking->created_by,
                $attributes['updated_by'] ?? $existingBooking->updated_by
            );

            // Validate conflicts excluding the current booking
            $conflictValidation = $this->conflictService->validateBookingConflicts($bookingDTO, $id);
            if (!$conflictValidation['valid']) {
                throw new \Exception('Booking update conflicts with existing reservations: ' . implode(', ', $conflictValidation['errors']), 409);
            }

            // Validate time gaps excluding the current booking
            $gapValidation = $this->conflictService->validateTimeGaps($bookingDTO, $id);
            if (!$gapValidation['valid']) {
                throw new \Exception('Booking update violates time gap requirements: ' . implode(', ', $gapValidation['errors']), 409);
            }

            Log::info('Booking updated with conflict validation', [
                'booking_id' => $id,
                'chef_id' => $bookingDTO->chef_id,
                'date' => $bookingDTO->date,
                'start_time' => $bookingDTO->start_time
            ]);
        }

        return $this->bookings->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->bookings->delete($id);
    }

    public function cancel($id, string $reason = 'cancelled_by_customer'): bool
    {
        $booking = $this->bookings->findOrFail($id);
        
        // Update booking status to cancelled
        $result = $this->bookings->update($id, [
            'booking_status' => $reason,
            'is_active' => false,
            'updated_at' => now()
        ]);

        // Repository update returns the model; convert to boolean success
        $success = (bool) $result;

        if ($success) {
            Log::info('Booking cancelled - slot liberated', [
                'booking_id' => $id,
                'chef_id' => $booking->chef_id,
                'date' => $booking->date,
                'start_time' => $booking->start_time,
                'reason' => $reason
            ]);
        }

        return $success;
    }

    public function checkAvailability(int $chefId, string $date, string $startTime, int $hoursCount, ?int $excludeBookingId = null): array
    {
        return $this->conflictService->checkChefAvailability($chefId, $date, $startTime, $hoursCount, $excludeBookingId);
    }

    public function getChefBookingsForDate(int $chefId, string $date): array
    {
        $bookings = $this->bookings->getChefBookingsInRange(
            $chefId, 
            Carbon::parse($date), 
            Carbon::parse($date)
        );

        return $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'date' => $booking->date->format('Y-m-d'),
                'start_time' => $booking->start_time->format('H:i'),
                'end_time' => $booking->end_time->format('H:i'),
                'hours_count' => $booking->hours_count,
                'booking_status' => $booking->booking_status,
                'customer_name' => $booking->customer ? $booking->customer->first_name . ' ' . $booking->customer->last_name : null
            ];
        })->toArray();
    }

    public function activate($id)
    {
        return $this->bookings->activate($id);
    }

    public function deactivate($id)
    {
        return $this->bookings->deactivate($id);
    }

    /**
     * Get query for bookings belonging to a specific user (as customer or chef)
     */
    public function getQueryForUser(int $userId)
    {
        return $this->bookings->query(['customer', 'chef.user', 'service', 'address'])
            ->where(function ($query) use ($userId) {
                $query->where('customer_id', $userId)
                      ->orWhereHas('chef', function ($q) use ($userId) {
                          $q->where('user_id', $userId);
                      });
            });
    }

    /**
     * Find a booking that belongs to a specific user (as customer or chef)
     */
    public function findForUser(int $bookingId, int $userId, array $with = [])
    {
        return $this->bookings->query($with)
            ->where('id', $bookingId)
            ->where(function ($query) use ($userId) {
                $query->where('customer_id', $userId)
                      ->orWhereHas('chef', function ($q) use ($userId) {
                          $q->where('user_id', $userId);
                      });
            })
            ->firstOrFail();
    }

    /**
     * Update a booking model directly (following AddressController pattern)
     */
    public function updateModel($booking, array $attributes)
    {
        // Check if time-related fields are being updated
        $timeFieldsChanged = isset($attributes['date']) || 
                           isset($attributes['start_time']) || 
                           isset($attributes['hours_count']);

        if ($timeFieldsChanged) {
            // Create DTO with updated values for conflict validation
            $bookingDTO = new BookingDTO(
                $booking->id,
                $attributes['customer_id'] ?? $booking->customer_id,
                $attributes['chef_id'] ?? $booking->chef_id,
                $attributes['chef_service_id'] ?? $booking->chef_service_id,
                $attributes['address_id'] ?? $booking->address_id,
                $attributes['date'] ?? $booking->date->format('Y-m-d'),
                $attributes['start_time'] ?? $booking->start_time->format('H:i'),
                $attributes['hours_count'] ?? $booking->hours_count,
                $attributes['number_of_guests'] ?? $booking->number_of_guests,
                $attributes['service_type'] ?? $booking->service_type,
                $attributes['unit_price'] ?? $booking->unit_price,
                $attributes['extra_guests_count'] ?? $booking->extra_guests_count,
                $attributes['extra_guests_amount'] ?? $booking->extra_guests_amount,
                $attributes['total_amount'] ?? $booking->total_amount,
                $attributes['commission_amount'] ?? $booking->commission_amount,
                $attributes['payment_status'] ?? $booking->payment_status,
                $attributes['booking_status'] ?? $booking->booking_status,
                $attributes['notes'] ?? $booking->notes,
                $attributes['is_active'] ?? $booking->is_active,
                $attributes['created_by'] ?? $booking->created_by,
                $attributes['updated_by'] ?? $booking->updated_by
            );

            // Validate conflicts excluding the current booking
            $conflictValidation = $this->conflictService->validateBookingConflicts($bookingDTO, $booking->id);
            if (!$conflictValidation['valid']) {
                throw new \Exception('Booking update conflicts with existing reservations: ' . implode(', ', $conflictValidation['errors']), 409);
            }

            // Validate time gaps excluding the current booking
            $gapValidation = $this->conflictService->validateTimeGaps($bookingDTO, $booking->id);
            if (!$gapValidation['valid']) {
                throw new \Exception('Booking update violates time gap requirements: ' . implode(', ', $gapValidation['errors']), 409);
            }

            Log::info('Booking updated with conflict validation', [
                'booking_id' => $booking->id,
                'chef_id' => $bookingDTO->chef_id,
                'date' => $bookingDTO->date,
                'start_time' => $bookingDTO->start_time
            ]);
        }

        return $this->bookings->update($booking->id, $attributes);
    }

    /**
     * Confirm a booking (for chef)
     */
    public function confirm($id)
    {
        return $this->bookings->update($id, [
            'booking_status' => 'confirmed',
            'updated_at' => now()
        ]);
    }

    /**
     * Complete a booking (for chef)
     */
    public function complete($id)
    {
        return $this->bookings->update($id, [
            'booking_status' => 'completed',
            'updated_at' => now()
        ]);
    }
}

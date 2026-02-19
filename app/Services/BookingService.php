<?php

namespace App\Services;

use App\DTOs\BookingDTO;
use App\Repositories\BookingRepository;
use Carbon\Carbon;

class BookingService
{
    protected BookingRepository $bookings;
    protected BookingConflictService $conflictService;
    protected DiscountCodeService $discountCodeService;

    public function __construct(
        BookingRepository $bookings,
        BookingConflictService $conflictService,
        DiscountCodeService $discountCodeService
    ) {
        $this->bookings = $bookings;
        $this->conflictService = $conflictService;
        $this->discountCodeService = $discountCodeService;
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
            null, // id
            $attributes['customer_id'] ?? null, // customer_id
            $attributes['chef_id'] ?? null, // chef_id
            $attributes['chef_service_id'] ?? null, // chef_service_id
            $attributes['address_id'] ?? null, // address_id
            $attributes['date'] ?? null, // date
            $attributes['start_time'] ?? null, // start_time
            $attributes['hours_count'] ?? null, // hours_count
            $attributes['number_of_guests'] ?? null, // number_of_guests
            $attributes['service_type'] ?? null, // service_type
            $attributes['unit_price'] ?? null, // unit_price
            $attributes['extra_guests_count'] ?? null, // extra_guests_count
            $attributes['extra_guests_amount'] ?? null, // extra_guests_amount
            $attributes['total_amount'] ?? null, // total_amount
            $attributes['commission_amount'] ?? null, // commission_amount
            $attributes['payment_status'] ?? 'pending', // payment_status
            $attributes['booking_status'] ?? 'pending', // booking_status
            null, // rejection_reason
            null, // cancellation_reason
            $attributes['discount_code_id'] ?? null, // discount_code_id
            null, // discount_code
            $attributes['discount_amount'] ?? 0, // discount_amount
            $attributes['original_amount'] ?? $attributes['total_amount'] ?? null, // original_amount
            $attributes['notes'] ?? null, // notes
            $attributes['is_active'] ?? true, // is_active
            $attributes['created_by'] ?? null, // created_by
            $attributes['updated_by'] ?? null // updated_by
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
        $result = $this->conflictService->createBookingWithLocking($bookingDTO);

        // If booking was created successfully and has a discount code, apply it
        if ($result['success'] && $bookingDTO->discount_code_id) {
            $this->discountCodeService->applyDiscount(
                $bookingDTO->discount_code_id,
                $bookingDTO->customer_id,
                $result['booking']->id,
                [
                    'original_amount' => $bookingDTO->original_amount,
                    'discount_amount' => $bookingDTO->discount_amount,
                    'final_amount' => $bookingDTO->total_amount,
                ]
            );
        }

        return $result;
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
                $id, // id
                $attributes['customer_id'] ?? $existingBooking->customer_id, // customer_id
                $attributes['chef_id'] ?? $existingBooking->chef_id, // chef_id
                $attributes['chef_service_id'] ?? $existingBooking->chef_service_id, // chef_service_id
                $attributes['address_id'] ?? $existingBooking->address_id, // address_id
                $attributes['date'] ?? $existingBooking->date->format('Y-m-d'), // date
                $attributes['start_time'] ?? $existingBooking->start_time->format('H:i'), // start_time
                $attributes['hours_count'] ?? $existingBooking->hours_count, // hours_count
                $attributes['number_of_guests'] ?? $existingBooking->number_of_guests, // number_of_guests
                $attributes['service_type'] ?? $existingBooking->service_type, // service_type
                $attributes['unit_price'] ?? $existingBooking->unit_price, // unit_price
                $attributes['extra_guests_count'] ?? $existingBooking->extra_guests_count, // extra_guests_count
                $attributes['extra_guests_amount'] ?? $existingBooking->extra_guests_amount, // extra_guests_amount
                $attributes['total_amount'] ?? $existingBooking->total_amount, // total_amount
                $attributes['commission_amount'] ?? $existingBooking->commission_amount, // commission_amount
                $attributes['payment_status'] ?? $existingBooking->payment_status, // payment_status
                $attributes['booking_status'] ?? $existingBooking->booking_status, // booking_status
                $attributes['rejection_reason'] ?? $existingBooking->rejection_reason, // rejection_reason
                $attributes['cancellation_reason'] ?? $existingBooking->cancellation_reason, // cancellation_reason
                $attributes['discount_code_id'] ?? $existingBooking->discount_code_id, // discount_code_id
                null, // discount_code
                $attributes['discount_amount'] ?? $existingBooking->discount_amount ?? 0, // discount_amount
                $attributes['original_amount'] ?? $existingBooking->original_amount, // original_amount
                $attributes['notes'] ?? $existingBooking->notes, // notes
                $attributes['is_active'] ?? $existingBooking->is_active, // is_active
                $attributes['created_by'] ?? $existingBooking->created_by, // created_by
                $attributes['updated_by'] ?? $existingBooking->updated_by // updated_by
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

            // booking updated with conflict validation (logging removed)
        }

        return $this->bookings->update($id, $attributes);
    }

    public function delete($id)
    {
        return $this->bookings->delete($id);
    }

    public function cancel($id, string $reason = 'cancelled_by_customer', ?string $cancellationReason = null): bool
    {
        $booking = $this->bookings->findOrFail($id);

        $updateData = [
            'booking_status' => $reason,
            'is_active' => false,
            'updated_at' => now()
        ];

        // Add cancellation_reason if provided
        if ($cancellationReason !== null) {
            $updateData['cancellation_reason'] = $cancellationReason;
        }

        // Update booking status to cancelled
        $result = $this->bookings->update($id, $updateData);

        // Repository update returns the model; convert to boolean success
        $success = (bool) $result;

        if ($success) {
            // booking cancelled (logging removed)
        }

        return $success;
    }

    public function checkAvailability(int $chefId, string $date, string $startTime, int $hoursCount, ?int $serviceId = null, ?int $excludeBookingId = null): array
    {
        return $this->conflictService->checkChefAvailability($chefId, $date, $startTime, $hoursCount, $serviceId, $excludeBookingId);
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
                $booking->id, // id
                $attributes['customer_id'] ?? $booking->customer_id, // customer_id
                $attributes['chef_id'] ?? $booking->chef_id, // chef_id
                $attributes['chef_service_id'] ?? $booking->chef_service_id, // chef_service_id
                $attributes['address_id'] ?? $booking->address_id, // address_id
                $attributes['date'] ?? $booking->date->format('Y-m-d'), // date
                $attributes['start_time'] ?? $booking->start_time->format('H:i'), // start_time
                $attributes['hours_count'] ?? $booking->hours_count, // hours_count
                $attributes['number_of_guests'] ?? $booking->number_of_guests, // number_of_guests
                $attributes['service_type'] ?? $booking->service_type, // service_type
                $attributes['unit_price'] ?? $booking->unit_price, // unit_price
                $attributes['extra_guests_count'] ?? $booking->extra_guests_count, // extra_guests_count
                $attributes['extra_guests_amount'] ?? $booking->extra_guests_amount, // extra_guests_amount
                $attributes['total_amount'] ?? $booking->total_amount, // total_amount
                $attributes['commission_amount'] ?? $booking->commission_amount, // commission_amount
                $attributes['payment_status'] ?? $booking->payment_status, // payment_status
                $attributes['booking_status'] ?? $booking->booking_status, // booking_status
                $attributes['rejection_reason'] ?? $booking->rejection_reason, // rejection_reason
                $attributes['cancellation_reason'] ?? $booking->cancellation_reason, // cancellation_reason
                $attributes['discount_code_id'] ?? $booking->discount_code_id, // discount_code_id
                null, // discount_code
                $attributes['discount_amount'] ?? $booking->discount_amount ?? 0, // discount_amount
                $attributes['original_amount'] ?? $booking->original_amount, // original_amount
                $attributes['notes'] ?? $booking->notes, // notes
                $attributes['is_active'] ?? $booking->is_active, // is_active
                $attributes['created_by'] ?? $booking->created_by, // created_by
                $attributes['updated_by'] ?? $booking->updated_by // updated_by
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

            // booking updated with conflict validation (logging removed)
        }

        return $this->bookings->update($booking->id, $attributes);
    }

    /**
     * Accept a booking (for chef)
     */
    public function accept($id)
    {
        return $this->bookings->update($id, [
            'booking_status' => 'accepted',
            'updated_at' => now()
        ]);
    }

    /**
     * Reject a booking (for chef)
     *
     * @param int $id Booking ID
     * @param string|null $rejectionReason Optional rejection reason
     * @return mixed Updated booking model
     */
    public function reject($id, ?string $rejectionReason = null)
    {
        $updateData = [
            'booking_status' => 'rejected',
            'is_active' => false,
            'updated_at' => now()
        ];

        // Add rejection reason if provided
        if ($rejectionReason !== null) {
            $updateData['rejection_reason'] = $rejectionReason;
        }

        return $this->bookings->update($id, $updateData);
    }

    /**
     * Reject a booking with a required reason
     *
     * @param int $id Booking ID
     * @param string $rejectionReason Rejection reason (required)
     * @return mixed Updated booking model
     */
    public function rejectWithReason($id, string $rejectionReason)
    {
        return $this->reject($id, $rejectionReason);
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

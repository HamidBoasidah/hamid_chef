<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{
    /**
     * Determine if the user can view any bookings.
     * We disallow global listing; controllers should scope listings to the
     * authenticated customer or chef.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine if the user can create bookings.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can view the booking.
     * Only the booking owner (customer) or the chef who owns the service may view it.
     */
    public function view(User $user, Booking $booking): bool
    {
        if ($booking->customer_id === $user->id) {
            return true;
        }

        if ($booking->chef !== null && $booking->chef->user_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can update the booking.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Allow customer who created it or chef who owns the service
        if ($booking->customer_id === $user->id) return true;
        if ($booking->chef !== null && $booking->chef->user_id === $user->id) return true;
        return false;
    }

    /**
     * Determine if the user can delete (cancel) the booking.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Same ownership rules for deletion/cancellation
        if ($booking->customer_id === $user->id) return true;
        if ($booking->chef !== null && $booking->chef->user_id === $user->id) return true;
        return false;
    }

    /**
     * Customer-specific cancel permission.
     */
    public function cancelByCustomer(User $user, Booking $booking): bool
    {
        return $booking->customer_id === $user->id;
    }

    /**
     * Chef-specific cancel permission.
     */
    public function cancelByChef(User $user, Booking $booking): bool
    {
        return $booking->chef !== null && $booking->chef->user_id === $user->id;
    }

    /**
     * Chef can accept a booking if he owns it.
     */
    public function accept(User $user, Booking $booking): bool
    {
        return $booking->chef !== null && $booking->chef->user_id === $user->id;
    }

    /**
     * Chef can reject a booking if he owns it.
     */
    public function reject(User $user, Booking $booking): bool
    {
        return $booking->chef !== null && $booking->chef->user_id === $user->id;
    }

    /**
     * Chef can complete a booking if he owns it.
     */
    public function complete(User $user, Booking $booking): bool
    {
        return $booking->chef !== null && $booking->chef->user_id === $user->id;
    }
}

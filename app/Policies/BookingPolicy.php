<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('bookings.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): bool
    {
        // Customers can only view their own bookings
        if ($user->isCustomer()) {
            return $booking->customer_id === $user->customer_id;
        }
        return $user->can('bookings.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('bookings.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): bool
    {
        // Customers can only update their own bookings in draft/pending status
        if ($user->isCustomer()) {
            return $booking->customer_id === $user->customer_id 
                && in_array($booking->status, ['draft', 'pending']);
        }
        return $user->can('bookings.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): bool
    {
        // Customers can only delete their own bookings in draft/pending status
        if ($user->isCustomer()) {
            return $booking->customer_id === $user->customer_id 
                && in_array($booking->status, ['draft', 'pending']);
        }
        return $user->can('bookings.delete');
    }

    /**
     * Determine whether the user can confirm the booking.
     */
    public function confirm(User $user, Booking $booking): bool
    {
        return $user->can('bookings.confirm');
    }

    /**
     * Determine whether the user can cancel the booking.
     */
    public function cancel(User $user, Booking $booking): bool
    {
        if ($user->isCustomer()) {
            return $booking->customer_id === $user->customer_id 
                && in_array($booking->status, ['draft', 'pending', 'confirmed']);
        }
        return $user->can('bookings.cancel');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): bool
    {
        return $user->can('bookings.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): bool
    {
        return $user->can('bookings.delete');
    }
}

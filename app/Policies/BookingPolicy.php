<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookingPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability)
    {
        if ($user->isAdmin) {
            return true;
        }
    }

    public function book(User $user, Booking $booking): bool
    {
        $event = $booking->event;

        if ($booking->user_id === $user->id && $booking->status === \App\Enums\BookingStatus::RESERVED) {
            return true;
        }

        if ($booking->user_id) {
            return false;
        }

        $now = now();

        // Determine the effective booking start
        $startBooking = ($user->is_preaccess && $event->preBooking)
            ? $event->preBooking
            : $event->startBooking;

        // Check if current time is within booking window
        if (! $now->between($startBooking, $event->endBooking)) {
            return false;
        }

        // Check if multiple bookings are allowed
        if (! $event->multiple_bookings_allowed &&
            $user->bookings->where('event_id', $event->id)->isNotEmpty()
        ) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the booking.
     *
     * @param  User  $user
     * @param  Booking  $booking
     * @return mixed
     */
    public function view(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }

    /**
     * Determine whether the user can create bookings.
     *
     * @param  User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return false;
    }

    /**
     * Determine whether the user can update the booking.
     *
     * @param  User  $user
     * @param  Booking  $booking
     * @return mixed
     */
    public function update(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id || empty($booking->user_id);
    }

    /**
     * Determine whether the user can delete the booking.
     *
     * @param  User  $user
     * @param  Booking  $booking
     * @return mixed
     */
    public function delete(User $user, Booking $booking)
    {
        return false;
    }

    /**
     * Determine whether the user can restore the booking.
     *
     * @param  User  $user
     * @param  Booking  $booking
     * @return mixed
     */
    public function restore(User $user, Booking $booking)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the booking.
     *
     * @param  User  $user
     * @param  Booking  $booking
     * @return mixed
     */
    public function forceDelete(User $user, Booking $booking)
    {
        return false;
    }

    /**
     * Determine whether the user can cancel the booking.
     *
     * @param  User  $user
     * @param  Booking  $booking
     * @return mixed
     */
    public function cancel(User $user, Booking $booking)
    {
        return $user->id === $booking->user_id;
    }

    public function approve(User $user, Booking $booking): bool
    {
        return $booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL;
    }

    public function decline(User $user, Booking $booking): bool
    {
        return $booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL;
    }
}

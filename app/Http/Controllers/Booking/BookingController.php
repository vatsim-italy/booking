<?php

namespace App\Http\Controllers\Booking;

use App\Enums\BookingStatus;
use App\Enums\EventType;
use App\Events\BookingCancelled;
use App\Events\BookingConfirmed;
use App\Events\BookingRequested;
use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\UpdateBooking;
use App\Models\Airport;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request, Event $event): View|RedirectResponse
    {
        return view('booking.overview', compact('event'));
    }

    public function show(Booking $booking): View
    {
        if ($booking->event->event_type_id == EventType::MULTIFLIGHTS->value) {
            return view('booking.show_multiflights', compact('booking'));
        }
        $flight = $booking->flights->first();
        $fullRotation = $booking->getFullRotation();

        return view('booking.show', compact('booking', 'flight', 'fullRotation'));
    }

    public function edit(Booking $booking): View|RedirectResponse
    {
        $userId = auth()->id();
        $user = auth()->user();

        if (! $user || ! $user->can('book', $booking)) {
            if ($booking->user_id && $booking->user_id !== $userId) {
                $message = 'This slot has already been taken.';
            } elseif (
                ! $booking->event->multiple_bookings_allowed &&
                $user->bookings->where('event_id', $booking->event->id)->isNotEmpty()
            ) {
                $message = 'You already have a booking for this event.';
            } elseif (now()->lt($booking->event->startBooking)) {
                $message = "Bookings aren\'t open yet. They open at "
                    . $booking->event->startBooking->format('d-m-Y Hi') . 'z';
            } elseif (now()->gt($booking->event->endBooking)) {
                $message = 'Bookings for this event are closed.';
            } else {
                $message = 'You cannot book this slot.';
            }

            return $this->redirectWithMessage('danger', 'Warning', $message, $booking);
        }

        $fullRotation = $booking->getFullRotation();

        if ($booking->is_request_slot) {
            $airports = Airport::all(['id', 'icao', 'name'])
            ->keyBy('id')
            ->map(fn($a) => [
                'icao' => $a->icao,
                'name' => $a->name,
                'display' => "{$a->icao} | {$a->name}"
            ]);

            if ($booking->status !== BookingStatus::UNASSIGNED && $booking->user_id !== $userId) {
                return $this->redirectWithMessage('danger', 'Warning', 'This request slot has already been taken by another user.', $booking);
            }
            $flight = $booking->flights->first();
        }


        // Reserve booking
        activity()->by($user)->on($booking)->log('Flight reserved');
        $booking->status = BookingStatus::RESERVED;
        $booking->user()->associate($user)->save();

        flashMessage('info', __('Slot reserved'), __('Slot remains reserved until :time', [
            'time' => $booking->updated_at->addMinutes(10)->format('Hi').'z',
        ]));

        if ($booking->is_request_slot) {
            return view('booking.request_reserved_slot', compact('booking', 'flight', 'airports'));
        }


        return $this->renderBookingView($booking, $fullRotation);
    }

    public function update(UpdateBooking $request, Booking $booking): RedirectResponse
    {
        if ($booking->is_request_slot) {

            //update flight data in case it's ZZZZ / Choose
            $flight = $booking->flights->first();

            if ($request->filled('dep')) {
                // Find airport by ICAO
                $airport = Airport::where(column: 'icao', operator: $request->dep)->first();
                if ($airport) {
                    $flight->dep = $airport->id;
                }
            }

            if ($request->filled('arr')) {
                $airport = Airport::where(column: 'icao', operator: $request->arr)->first();
                if ($airport) {
                    $flight->arr = $airport->id;
                }
            }

                        // Fill with user-submitted data
            $booking->fill([
                'callsign' => $request->callsign,
                'acType' => $request->acType,
                'dep' => $flight->dep,
                'arr' => $flight->arr,
            ]);

            $flight->save();

        }

        // This check should actually be in the policy, but is now here as a quick fix
        if ($booking->user_id === $request->user()->id) {

            if ($booking->is_editable) {
                $booking->fill([
                    'callsign' => $request->callsign,
                    'acType' => $request->acType,
                ]);
            }

            if ($booking->event->is_oceanic_event) {
                $booking->selcal = $this->validateSELCAL(
                    strtoupper($request->selcal1.'-'.$request->selcal2),
                    $booking->event_id
                );
            }

            if ($booking->status == BookingStatus::RESERVED) {
                $booking->status = BookingStatus::BOOKED;
                $booking->save();
                event(new BookingConfirmed($booking));
                if (! Str::contains(config('mail.default'), ['log', 'array'])) {
                    $message = __('Your booking has been confirmed. You should shortly receive an email with your booking details. Be sure to also check your spam folder.');
                } else {
                    $message = __('Your booking has been confirmed');
                }

                flashMessage(
                    'success',
                    __('Booking confirmed!'),
                    $message
                );
            } else {
                $booking->save();
                flashMessage('success', __('Booking edited!'), __('Booking has been edited!'));
            }

            return to_route('bookings.event.index', $booking->event);
        } else {
            abort(403);
        }
    }

    private function redirectWithMessage(string $level, string $title, string $message, Booking $booking): RedirectResponse
    {
        flashMessage($level, __($title), __($message));

        return to_route('bookings.event.index', $booking->event);
    }

    private function renderBookingView(Booking $booking, $fullRotation): View
    {
        if ($booking->event->event_type_id == EventType::MULTIFLIGHTS->value) {
            return view('booking.edit_multiflights', compact('booking'));
        }

        $flight = $booking->flights->first();

        return view('booking.edit', compact('booking', 'flight', 'fullRotation'));
    }

    private function userHasBookingOrReservation(Booking $booking, string $status): bool
    {
        return auth()->user()->bookings
            ->where('event_id', $booking->event_id)
            ->where('status', $status)
            ->isNotEmpty();
    }

    public function validateSELCAL($selcal, $eventId): ?string
    {
        // Separate characters
        $char1 = substr($selcal, 0, 1);
        $char2 = substr($selcal, 1, 1);
        $char3 = substr($selcal, 3, 1);
        $char4 = substr($selcal, 4, 1);

        // Check if SELCAL has valid format
        if (! preg_match('/[ABCDEFGHJKLMPQRS]{2}[-][ABCDEFGHJKLMPQRS]{2}/', $selcal)) {
            return null;
        }

        // Check if each character is unique
        if (substr_count($selcal, $char1) > 1 || substr_count($selcal, $char2) > 1 || substr_count(
            $selcal,
            $char3
        ) > 1 || substr_count($selcal, $char4) > 1) {
            return null;
        }

        // Check if characters per pair are in alphabetical order
        if ($char1 > $char2 || $char3 > $char4) {
            return null;
        }

        // Check for duplicates within the same event
        if (Booking::where('event_id', $eventId)
            ->where('selcal', '=', $selcal)
            ->first()
        ) {
            return null;
        }

        return $selcal;
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);
        if ($booking->event->endBooking > now()) {
            if ($booking->is_editable || $booking->is_request_slot) {
                $booking->fill([
                    'callsign' => null,
                    'acType' => null,
                    'selcal' => null,
                ]);
            }

            if ($booking->status == BookingStatus::BOOKED) {
                event(new BookingCancelled($booking, auth()->user()));
                $title = __('Booking cancelled!');
                $message = __('Booking has been cancelled!');
            } else {
                $title = __('Slot free');
                $message = __('Slot is now free to use again');
            }

            $booking->status = BookingStatus::UNASSIGNED;
            flashMessage('info', $title, $message);
            $booking->user()->dissociate()->save();

            return to_route('bookings.event.index', $booking->event);
        }
        flashMessage(
            'danger',
            __('Danger'),
            __('Bookings have been locked at :time', ['time' => $booking->event->endBooking->format('d-m-Y Hi').'z'])
        );

        return to_route('bookings.event.index', $booking->event);
    }
}

<?php

use App\Enums\BookingStatus;
use App\Events\BookingConfirmed;
use Illuminate\Support\Facades\Event as EventFacade;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Flight;
use App\Models\User;
use Tests\TestCase;

function openSlot(): Booking
{
    $event = Event::factory()->create(['startBooking' => now()->subDay(), 'endBooking' => now()->addDay()]);
    $booking = Booking::factory()->unassigned()->create(['event_id' => $event->id]);
    Flight::factory()->create(['booking_id' => $booking->id, 'dep' => $event->dep, 'arr' => $event->arr]);

    return $booking->fresh();
}

it('reserves a free slot for the first user only', function () {
    /** @var TestCase $this */
    $booking = openSlot();
    [$first, $second] = User::factory()->count(2)->create();

    $this->actingAs($first)->get(route('bookings.edit', $booking))->assertOk();
    expect($booking->fresh())
        ->status->toBe(BookingStatus::RESERVED)
        ->user_id->toBe($first->id);

    $this->actingAs($second)->get(route('bookings.edit', $booking))
        ->assertRedirect(route('bookings.event.index', $booking->event));
    expect($booking->fresh()->user_id)->toBe($first->id);
});

it('refuses to confirm a slot the user no longer holds', function () {
    /** @var TestCase $this */
    $booking = openSlot();
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('bookings.edit', $booking))->assertOk();
    // the reservation expired and was freed before the user submitted
    $booking->fresh()->forceFill(['status' => BookingStatus::UNASSIGNED, 'user_id' => null])->save();

    $this->actingAs($user)->patch(route('bookings.update', $booking))->assertForbidden();
    expect($booking->fresh()->status)->toBe(BookingStatus::UNASSIGNED);
});

it('confirms a reserved slot', function () {
    /** @var TestCase $this */
    EventFacade::fake([BookingConfirmed::class]);
    $booking = openSlot();
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('bookings.edit', $booking));
    $this->actingAs($user)->patch(route('bookings.update', $booking))
        ->assertRedirect(route('bookings.event.index', $booking->event));

    expect($booking->fresh())
        ->status->toBe(BookingStatus::BOOKED)
        ->user_id->toBe($user->id);
    EventFacade::assertDispatched(BookingConfirmed::class);
});

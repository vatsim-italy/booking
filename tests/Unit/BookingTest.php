<?php

namespace Tests\Unit;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Flight;
use Tests\TestCase;

it('can creates new booking', function () {
    /** @var TestCase $this */

    /** @var Flight $flight */
    $flight = Flight::factory()->create();

    $this->assertDatabaseHas('flights', [
        'id' => $flight->id,
        'booking_id' => $flight->booking_id,
        'dep' => $flight->dep,
        'arr' => $flight->arr,
    ]);

    $this->assertDatabaseHas('bookings', [
        'id' => $flight->booking->id,
        'event_id' => $flight->booking->event_id,
    ]);
});

it('does not infinite loop when turnaround bookings reference each other', function () {
    /** @var TestCase $this */

    $event = Event::factory()->create();

    /** @var Booking $bookingA */
    $bookingA = Booking::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'AAA123',
        'turnaroundCS' => 'BBB456',
    ]);

    /** @var Booking $bookingB */
    $bookingB = Booking::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'BBB456',
        'turnaroundCS' => 'AAA123', // points back to A -> cycle
    ]);

    $rotation = $bookingA->getFullRotation();

    expect($rotation)->toHaveCount(2);
});

it('does not infinite loop when a booking references its own callsign', function () {
    /** @var TestCase $this */

    $event = Event::factory()->create();

    $booking = Booking::factory()->create([
        'event_id' => $event->id,
        'callsign' => 'CCC789',
        'turnaroundCS' => 'CCC789',
    ]);

    $rotation = $booking->getFullRotation();

    expect($rotation)->toHaveCount(1);
});
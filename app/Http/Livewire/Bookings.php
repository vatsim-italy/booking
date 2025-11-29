<?php

namespace App\Http\Livewire;

use App\Models\Event;
use Livewire\Component;
use App\Enums\BookingStatus;

class Bookings extends Component
{
    public Event $event;
    public int $refreshInSeconds = 0;
    public $bookings;
    public ?string $filter = null;
    public int $total = 0;
    public int $booked = 0;

    public function filter($filter): void
    {
        $this->filter = strtolower($filter);
    }

    public function mount(): void
    {
        // Only enable polling if event is 'active'
        if (now()->between($this->event->startBooking, $this->event->endEvent)) {
            $this->refreshInSeconds = 15;
        }
    }

    public function render()
    {
        $filter = $this->filter;
        // @TODO Check should actually be in a policy
        if ($this->event->is_online || auth()->check() && auth()->user()->isAdmin) {
            $this->bookings = $this->event->bookings()
            ->with([
                'event',
                'user',
                'flights',
                'flights.airportDep',
                'flights.airportArr',
            ])
            ->get()
            ->sortBy(function($booking) {
                return $booking->flights->map(function($f) {
                    if ($f->airportDep->icao === 'LIPZ') return $f->ctot;
                    if ($f->airportArr->icao === 'LIPZ') return $f->eta;
                    return $f->ctot;
                })->min();
            });
        } else {
            abort_unless(auth()->check() && auth()->user()->isAdmin, 404);
        }

        $this->booked = $this->bookings->where('status', BookingStatus::BOOKED)->count();

        $this->total = $this->bookings->count();

        // https://github.com/TomasVotruba/bladestan/issues/65#issuecomment-1582383622
        return view('livewire.bookings', [
            'event' => $this->event,
            'refreshInSeconds' => $this->refreshInSeconds,
            'bookings' => $this->bookings,
            'filter' => $this->filter,
            'total' => $this->total,
            'booked' => $this->booked,
        ]);
    }
}

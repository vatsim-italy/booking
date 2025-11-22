<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Events\BookingDeclined;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingDeclinedNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  BookingDeclined  $event
     * @return void
     */
    public function handle(BookingDeclined $event): void
    {
        activity()
            ->by(auth()->user())
            ->on($event->booking)
            ->log('Flight declined');

        $event->booking->user->notify(new \App\Notifications\BookingDeclined($event->booking, $event->reason));
    }
}

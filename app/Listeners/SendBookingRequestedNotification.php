<?php

namespace App\Listeners;

use App\Notifications\BookingRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendBookingRequestedNotification implements ShouldQueue
{
    public function handle($event): void
    {
        $adminMail = $event->booking->event->mail;

        if (!$adminMail) {
            // no mail configured
            return;
        }

        activity()
            ->by(auth()->user())
            ->on($event->booking)
            ->log('Booking requested');

        // This actually sends the email
        Notification::route('mail', $adminMail)
            ->notify(new BookingRequested($event->booking, $adminMail));
    }
}

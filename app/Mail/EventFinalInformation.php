<?php

namespace App\Mail;

use App\Booking;
use Illuminate\{
    Bus\Queueable, Mail\Mailable, Queue\SerializesModels, Contracts\Queue\ShouldQueue
};

class EventFinalInformation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The booking instance.
     *
     * @var Booking
     */
    public $booking;

    /**
     * Create a new message instance.
     *
     * @param Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.event.finalInformation');
    }
}

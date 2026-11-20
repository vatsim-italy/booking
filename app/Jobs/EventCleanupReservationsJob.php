<?php

namespace App\Jobs;

use App\Models\Event;
use App\Models\Booking;
use App\Enums\BookingStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class EventCleanupReservationsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Event $event)
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->event->bookings()
            ->whereStatus(BookingStatus::RESERVED->value)
            ->each(function (Booking $booking) {
                // Re-read under lock: the user may have confirmed or renewed it since the query ran.
                DB::transaction(function () use ($booking) {
                    $booking = Booking::whereKey($booking->id)->lockForUpdate()->first();
                    if ($booking?->status === BookingStatus::RESERVED
                        && now()->greaterThanOrEqualTo($booking->updated_at->addMinutes(10))) {
                        $booking->status = BookingStatus::UNASSIGNED;
                        $booking->user_id = null;
                        $booking->save();
                    }
                });
            });
    }
}

<div class="modal fade" id="declineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="declineForm" action="">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Decline Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label">Reason for decline (optional)</label>
                    <textarea name="reason" class="form-control" rows="3"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Decline</button>
                </div>
            </div>

        </form>
    </div>
</div>
<thead>
    <tr>
        <th scope="row">From</th>
        <th scope="row">To</th>
        @if ($event->uses_times)
            <th scope="row"><abbr title="Scheduled Take Off Time">STD</abbr></th>
            <th scope="row"><abbr title="Scheduled Time of Arrival">STA</abbr></th>
        @endif
        <th scope="row">Callsign 
        </th>
        <th scope="row">Aircraft</th>
        <th scope="row">Book | Available until {{ $event->endBooking->format('d-m-Y H:i') }}z</th>
        @if (auth()->check() && auth()->user()->isAdmin && $event->endEvent >= now())
            <th colspan="3" scope="row">Admin actions</th>
        @endif
    </tr>
</thead>
@foreach ($bookings as $booking)
    @php
        $flight = $booking->flights->first();
    @endphp
    {{-- @TODO Temp fix for events using filter buttons --}}
    @if ($flight)
        {{-- Check if flight belongs to the logged in user --}}
        <tr class="{{ auth()->check() && $booking->user_id == auth()->id() ? 'table-active' : '' }}"
            data-status="{{ $booking->status }}"
            data-reserved-type="{{ $booking->is_request_slot ? : 0 }}"
            data-dep="{{ $flight->airportDep->icao }}"
            data-arr="{{ $flight->airportArr->icao }}">

            <td>
                {!! str_replace('Airport', '', $flight->airportDep->fullName) !!}
            </td>
            <td>
                {!! str_replace('Airport', '', $flight->airportArr->fullName) !!}
            </td>
            @if ($booking->event->uses_times)
                <td>
                    {{ $flight->formattedCtot }}
                </td>
                <td>
                    {{ $flight->formattedEta }}
                </td>
            @endif
            <td class="{{ auth()->check() && auth()->user()->use_monospace_font ? 'text-monospace' : '' }}">
                <span class="text-nowrap">
                    {{ $booking->formatted_callsign }}&nbsp;
                    @if ($booking->is_request_slot && $booking->formatted_callsign)
                        <i class="fas fa-star text-info ms-1 align-middle" title="Turnaround available"></i>
                    @endif
                    @if ($booking->turnaroundCS)
                        <i class="fas fa-sync-alt text-info ms-1 align-middle" title="Turnaround available"></i>
                    @endif
                </span>
            </td>
            <td class="{{ auth()->check() && auth()->user()->use_monospace_font ? 'text-monospace' : '' }}">
                {{ $booking->formatted_actype }}</td>
            <td>
                @if ($booking->status == \App\Enums\BookingStatus::BOOKED)
                    @if (auth()->check() && $booking->user_id == auth()->id())
                        <a href="{{ route('bookings.show', $booking) }}" class="btn btn-info">
                            My booking
                        </a>
                    @else
                        <button class="btn btn-dark disabled">
                            Booked [{{ $booking->user->id }}]
                        </button>
                    @endif

                @elseif ($booking->status === \App\Enums\BookingStatus::RESERVED)
                    @can('update', $booking)
                        {{-- Check if a booking has been reserved by current user --}}
                        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-info">
                            My reservation
                        </a>
                    @else
                        <button class="btn btn-dark disabled">
                            Reserved
                            {{ auth()->check() && auth()->user()->isAdmin ? '[' . $booking->user->pic . ']' : '' }}</button>
                    @endcan
                @elseif ($booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL)
                    <button class="btn btn-dark disabled">
                        Pending approval
                    </button>
                @else
                    {{-- Available bookings --}}
                    @if(auth()->check())
                        @can('book', $booking)
                            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-success">
                                BOOK NOW
                            </a>
                        @else
                            @if(! $booking->event->multiple_bookings_allowed && auth()->user()->bookings->where('event_id', $booking->event->id)->isNotEmpty())
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-danger">
                                    You already have a booking
                                </a>
                            @else
                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-danger">
                                    Closed
                                </a>
                            @endif
                        @endcan
                    @else
                        <a href="{{ route('login', ['booking' => $booking]) }}" class="btn btn-info">
                            Click here to login
                        </a>
                    @endif
                @endif
            </td>
            @if (auth()->check() && auth()->user()->isAdmin && $event->endEvent >= now())
                <td data-label="Admin Actions">
                    <div class="admin-actions">
                        <a href="{{ route('admin.bookings.edit', $booking) }}" class="btn btn-info btn-sm">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.bookings.destroy', $booking) }}" method="post" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger btn-sm delete-booking">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                        @if ($booking->user_id)
                            <a href="mailto:{{ $booking->user->email }}" class="btn btn-info btn-sm">
                                <i class="fas fa-envelope"></i> Email [{{ $booking->user->email }}]
                            </a>
                        @endif
                    </div>
                </td>
            @endif
        </tr>
    @endif
@endforeach

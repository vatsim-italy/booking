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
            <th scope="row"><abbr title="Calculated Take Off Time">CTOT</abbr></th>
            <th scope="row"><abbr title="Estimated Time of Arrival">ETA</abbr></th>
        @endif
        <th scope="row">Callsign</th>
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
        <tr class="{{ auth()->check() && $booking->user_id == auth()->id() ? 'table-active' : '' }}">
            <td>
                {!! $flight->airportDep->fullName !!}
            </td>
            <td>
                {!! $flight->airportArr->fullName !!}
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
                        @if($booking->event->startBooking <= now() && $booking->event->endBooking >= now())
                            @if($booking->event->multiple_bookings_allowed || auth()->user()->bookings->where('event_id', $booking->event->id)->isEmpty())
                                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-success">BOOK NOW</a>
                            @else
                                <span class="text-danger">
                        <i class="fas fa-exclamation-circle me-1"></i>You already have a booking
                    </span>
                            @endif
                        @else
                            <span class="status-badge me-2" style="background-color: rgba(108, 117, 125, 0.1); color: #6c757d; border: 1px solid rgba(108, 117, 125, 0.2);">
                    <i class="fas fa-ban me-1"></i>Not Available
                </span>
                            <button class="btn btn-danger btn-sm" disabled>
                                <i class="fas fa-times"></i> Closed
                            </button>
                        @endif
                    @else
                        <a href="{{ route('login', ['booking' => $booking]) }}" class="btn btn-info">Click here to
                            login</a><!DOCTYPE html>
                        <html lang="en">
                        <head>
                            <meta charset="UTF-8">
                            <meta name="viewport" content="width=device-width, initial-scale=1.0">
                            <title>Flight Bookings</title>
                            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
                            <style>
                                :root {
                                    --primary-color: #0d6efd;
                                    --success-color: #198754;
                                    --danger-color: #dc3545;
                                    --warning-color: #ffc107;
                                    --info-color: #0dcaf0;
                                    --dark-color: #212529;
                                    --light-color: #f8f9fa;
                                    --border-radius: 8px;
                                    --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                                    --transition: all 0.3s ease;
                                }

                                body {
                                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                                    background-color: #f5f7fa;
                                    color: #333;
                                    line-height: 1.6;
                                }

                                .booking-container {
                                    background: white;
                                    border-radius: var(--border-radius);
                                    box-shadow: var(--box-shadow);
                                    overflow: hidden;
                                    margin-bottom: 2rem;
                                }

                                .booking-header {
                                    background: linear-gradient(135deg, var(--primary-color), #0a58ca);
                                    color: white;
                                    padding: 1.5rem;
                                    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
                                }

                                .booking-title {
                                    font-weight: 700;
                                    margin: 0;
                                    font-size: 1.75rem;
                                }

                                .booking-subtitle {
                                    opacity: 0.9;
                                    margin: 0.5rem 0 0;
                                    font-size: 1rem;
                                }

                                .table-responsive {
                                    overflow-x: auto;
                                }

                                .table {
                                    margin: 0;
                                    border-collapse: separate;
                                    border-spacing: 0;
                                }

                                .table thead th {
                                    background-color: #e9ecef;
                                    border-bottom: 2px solid #dee2e6;
                                    font-weight: 600;
                                    padding: 1rem 0.75rem;
                                    vertical-align: middle;
                                    color: #495057;
                                }

                                .table tbody tr {
                                    transition: var(--transition);
                                }

                                .table tbody tr:hover {
                                    background-color: rgba(13, 110, 253, 0.05);
                                }

                                .table tbody tr.table-active {
                                    background-color: rgba(13, 110, 253, 0.15);
                                    border-left: 4px solid var(--primary-color);
                                }

                                .table tbody td {
                                    padding: 1rem 0.75rem;
                                    vertical-align: middle;
                                    border-top: 1px solid #dee2e6;
                                }

                                .airport-info {
                                    font-weight: 500;
                                }

                                .airport-code {
                                    font-weight: 700;
                                    color: var(--primary-color);
                                }

                                .time-info {
                                    font-family: monospace;
                                    font-weight: 600;
                                    color: var(--dark-color);
                                }

                                .callsign {
                                    font-weight: 700;
                                    color: var(--dark-color);
                                }

                                .turnaround-icon {
                                    color: var(--info-color);
                                    font-size: 0.9rem;
                                }

                                .aircraft-type {
                                    font-weight: 500;
                                    color: #495057;
                                }

                                .btn {
                                    border-radius: 6px;
                                    font-weight: 500;
                                    padding: 0.5rem 1rem;
                                    transition: var(--transition);
                                    display: inline-flex;
                                    align-items: center;
                                    justify-content: center;
                                    gap: 0.5rem;
                                }

                                .btn-success {
                                    background: linear-gradient(to right, var(--success-color), #157347);
                                    border: none;
                                }

                                .btn-success:hover {
                                    transform: translateY(-2px);
                                    box-shadow: 0 4px 8px rgba(25, 135, 84, 0.3);
                                }

                                .btn-info {
                                    background: linear-gradient(to right, var(--info-color), #0aa2c0);
                                    border: none;
                                }

                                .btn-info:hover {
                                    transform: translateY(-2px);
                                    box-shadow: 0 4px 8px rgba(13, 202, 240, 0.3);
                                }

                                .btn-dark {
                                    background: linear-gradient(to right, #6c757d, #5a6268);
                                    border: none;
                                }

                                .btn-danger {
                                    background: linear-gradient(to right, var(--danger-color), #c82333);
                                    border: none;
                                }

                                .btn-danger:hover {
                                    transform: translateY(-2px);
                                    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
                                }

                                .status-badge {
                                    display: inline-block;
                                    padding: 0.35rem 0.65rem;
                                    border-radius: 50px;
                                    font-size: 0.75rem;
                                    font-weight: 600;
                                    text-transform: uppercase;
                                    letter-spacing: 0.5px;
                                }

                                .badge-booked {
                                    background-color: rgba(25, 135, 84, 0.1);
                                    color: var(--success-color);
                                    border: 1px solid rgba(25, 135, 84, 0.2);
                                }

                                .badge-reserved {
                                    background-color: rgba(255, 193, 7, 0.1);
                                    color: #b38b00;
                                    border: 1px solid rgba(255, 193, 7, 0.2);
                                }

                                .badge-available {
                                    background-color: rgba(13, 110, 253, 0.1);
                                    color: var(--primary-color);
                                    border: 1px solid rgba(13, 110, 253, 0.2);
                                }

                                .admin-actions {
                                    display: flex;
                                    gap: 0.5rem;
                                    flex-wrap: wrap;
                                }

                                .text-monospace {
                                    font-family: 'Courier New', Courier, monospace;
                                }

                                .text-nowrap {
                                    white-space: nowrap;
                                }

                                .booking-closes {
                                    background-color: #fff3cd;
                                    border: 1px solid #ffeaa7;
                                    border-radius: var(--border-radius);
                                    padding: 0.75rem 1rem;
                                    margin-bottom: 1.5rem;
                                    display: flex;
                                    align-items: center;
                                    gap: 0.75rem;
                                }

                                .booking-closes i {
                                    color: #856404;
                                    font-size: 1.25rem;
                                }

                                .empty-state {
                                    text-align: center;
                                    padding: 3rem 2rem;
                                    color: #6c757d;
                                }

                                .empty-state i {
                                    font-size: 3rem;
                                    margin-bottom: 1rem;
                                    color: #dee2e6;
                                }

                                @media (max-width: 768px) {
                                    .table thead {
                                        display: none;
                                    }

                                    .table tbody tr {
                                        display: block;
                                        margin-bottom: 1rem;
                                        border: 1px solid #dee2e6;
                                        border-radius: var(--border-radius);
                                        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                                    }

                                    .table tbody td {
                                        display: flex;
                                        justify-content: space-between;
                                        align-items: center;
                                        padding: 0.75rem 1rem;
                                        border-top: none;
                                        border-bottom: 1px solid #e9ecef;
                                    }

                                    .table tbody td:before {
                                        content: attr(data-label);
                                        font-weight: 600;
                                        color: #495057;
                                        flex: 0 0 40%;
                                    }

                                    .table tbody td:last-child {
                                        border-bottom: none;
                                    }

                                    .admin-actions {
                                        flex-direction: column;
                                    }

                                    .btn {
                                        width: 100%;
                                        justify-content: center;
                                    }
                                }
                            </style>
                        </head>
                        <body>
                        <div class="container my-4">
                            <div class="booking-container">
                                <div class="booking-header">
                                    <h1 class="booking-title">Flight Bookings</h1>
                                    <p class="booking-subtitle">Book your flights for the upcoming event</p>
                                </div>

                                <div class="booking-closes">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <strong>Booking closes:</strong> {{ $event->endBooking->format('d-m-Y H:i') }}z
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                        <tr>
                                            <th scope="col">From</th>
                                            <th scope="col">To</th>
                                            @if ($event->uses_times)
                                                <th scope="col"><abbr title="Calculated Take Off Time">CTOT</abbr></th>
                                                <th scope="col"><abbr title="Estimated Time of Arrival">ETA</abbr></th>
                                            @endif
                                            <th scope="col">Callsign</th>
                                            <th scope="col">Aircraft</th>
                                            <th scope="col">Status</th>
                                            @if (auth()->check() && auth()->user()->isAdmin && $event->endEvent >= now())
                                                <th scope="col" colspan="3">Admin Actions</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($bookings as $booking)
                                            @php
                                                $flight = $booking->flights->first();
                                            @endphp
                                            @if ($flight)
                                                <tr class="{{ auth()->check() && $booking->user_id == auth()->id() ? 'table-active' : '' }}">
                                                    <td data-label="From">
                                                        <div class="airport-info">
                                                            <span class="airport-code">{{ $flight->airportDep->icao }}</span>
                                                            <div class="airport-name">{{ $flight->airportDep->name }}</div>
                                                        </div>
                                                    </td>
                                                    <td data-label="To">
                                                        <div class="airport-info">
                                                            <span class="airport-code">{{ $flight->airportArr->icao }}</span>
                                                            <div class="airport-name">{{ $flight->airportArr->name }}</div>
                                                        </div>
                                                    </td>
                                                    @if ($booking->event->uses_times)
                                                        <td data-label="CTOT" class="time-info">
                                                            {{ $flight->formattedCtot }}
                                                        </td>
                                                        <td data-label="ETA" class="time-info">
                                                            {{ $flight->formattedEta }}
                                                        </td>
                                                    @endif
                                                    <td data-label="Callsign" class="{{ auth()->check() && auth()->user()->use_monospace_font ? 'text-monospace' : '' }}">
                                        <span class="callsign text-nowrap">
                                            {{ $booking->formatted_callsign }}
                                            @if ($booking->turnaroundCS)
                                                <i class="fas fa-sync-alt turnaround-icon ms-1" title="Turnaround available"></i>
                                            @endif
                                        </span>
                                                    </td>
                                                    <td data-label="Aircraft" class="{{ auth()->check() && auth()->user()->use_monospace_font ? 'text-monospace' : '' }} aircraft-type">
                                                        {{ $booking->formatted_actype }}
                                                    </td>
                                                    <td data-label="Status">
                                                        @if ($booking->status == \App\Enums\BookingStatus::BOOKED)
                                                            @if (auth()->check() && $booking->user_id == auth()->id())
                                                                <span class="status-badge badge-booked me-2">
                <i class="fas fa-check-circle me-1"></i>My Booking
            </span>
                                                                <a href="{{ route('bookings.show', $booking) }}" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                            @else
                                                                <span class="status-badge badge-booked me-2">
                <i class="fas fa-user-check me-1"></i>Booked
            </span>
                                                                <button class="btn btn-dark btn-sm" disabled>
                                                                    [{{ $booking->user->id }}]
                                                                </button>
                                                            @endif

                                                        @elseif ($booking->status === \App\Enums\BookingStatus::RESERVED)
                                                            @can('update', $booking)
                                                                <span class="status-badge badge-reserved me-2">
                <i class="fas fa-clock me-1"></i>My Reservation
            </span>
                                                                <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                            @else
                                                                <span class="status-badge badge-reserved me-2">
                <i class="fas fa-user-clock me-1"></i>Reserved
            </span>
                                                                <button class="btn btn-dark btn-sm" disabled>
                                                                    {{ auth()->check() && auth()->user()->isAdmin ? '[' . $booking->user->pic . ']' : '' }}
                                                                </button>
                                                            @endcan

                                                        @elseif ($booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL)
                                                            <span class="status-badge badge-warning me-2">
            <i class="fas fa-hourglass-half me-1"></i>Pending Approval
        </span>

                                                        @else
                                                            {{-- Available bookings --}}
                                                            @if(auth()->check())
                                                                @if($booking->event->startBooking <= now() && $booking->event->endBooking >= now())
                                                                    @if($booking->event->multiple_bookings_allowed || auth()->user()->bookings->where('event_id', $booking->event->id)->isEmpty())
                                                                        <span class="status-badge badge-available me-2">
                                                                            <i class="fas fa-plane me-1"></i>Available
                                                                        </span>
                                                                        <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-success btn-sm">
                                                                            <i class="fas fa-bookmark"></i> BOOK NOW
                                                                        </a>
                                                                    @else
                                                                        <span class="text-danger">
                                                                            <i class="fas fa-exclamation-circle me-1"></i>You already have a booking
                                                                        </span>
                                                                    @endif
                                                                @else
                                                                    <span class="status-badge me-2" style="background-color: rgba(108, 117, 125, 0.1); color: #6c757d; border: 1px solid rgba(108, 117, 125, 0.2);">
                    <i class="fas fa-ban me-1"></i>Not Available
                </span>
                                                                    <button class="btn btn-danger btn-sm" disabled>
                                                                        <i class="fas fa-times"></i> Closed
                                                                    </button>
                                                                @endif
                                                            @else
                                                                <span class="status-badge badge-available me-2">
                <i class="fas fa-plane me-1"></i>Available
            </span>
                                                                <a href="{{ route('login', ['booking' => $booking]) }}" class="btn btn-info btn-sm">
                                                                    <i class="fas fa-sign-in-alt"></i> Login to Book
                                                                </a>
                                                            @endif
                                                        @endif
                                                    </td>                                                    @if (auth()->check() && auth()->user()->isAdmin && $event->endEvent >= now())
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
                                                                        <i class="fas fa-envelope"></i> Email
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endforeach

                                        @if($bookings->isEmpty())
                                            <tr>
                                                <td colspan="{{ $event->uses_times ? 7 : 5 }}" class="text-center">
                                                    <div class="empty-state">
                                                        <i class="fas fa-plane-slash"></i>
                                                        <h3>No Bookings Available</h3>
                                                        <p>There are currently no flights available for booking.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                        <script>
                            // Add confirmation for delete actions
                            document.addEventListener('DOMContentLoaded', function() {
                                const deleteButtons = document.querySelectorAll('.delete-booking');

                                deleteButtons.forEach(button => {
                                    button.addEventListener('click', function(e) {
                                        if (!confirm('Are you sure you want to delete this booking? This action cannot be undone.')) {
                                            e.preventDefault();
                                        }
                                    });
                                });

                                // Add tooltips for abbreviations
                                const abbrs = document.querySelectorAll('abbr');
                                abbrs.forEach(abbr => {
                                    abbr.setAttribute('data-bs-toggle', 'tooltip');
                                });

                                // Initialize Bootstrap tooltips
                                const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                                const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                                    return new bootstrap.Tooltip(tooltipTriggerEl);
                                });
                            });
                        </script>
                        </body>
                        </html>
                    @endif
                @endif
            </td>
            @if (auth()->check() && auth()->user()->isAdmin && $event->endEvent >= now())
                <td data-label="Admin Actions">
                    <div class="admin-actions">
                        @if($booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL)
                            <a href="{{ route('admin.bookings.approve', $booking) }}" class="btn btn-success btn-sm">
                                <i class="fa fa-check"></i> Approve
                            </a>
                            <a href="{{ route('admin.bookings.decline', $booking) }}" class="btn btn-danger btn-sm decline-booking">
                                Decline
                            </a>
                        @endif
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

@php
    use App\Enums\BookingStatus;
@endphp

<!-- Turnaround Information -->
@if($fullRotation && $fullRotation->isNotEmpty() && $fullRotation->count() > 1)
    <div class="card mb-4">
        <div class="card-header py-2 d-flex align-items-center">
            <i class="fas fa-sync-alt text-info mr-2" aria-hidden="true"></i>
            <strong>{{ __('Turnaround Rotation') }}</strong>
            <span class="badge badge-pill badge-light ml-auto">{{ $fullRotation->count() }} {{ __('flights') }}</span>
        </div>

        <ul class="list-group list-group-flush">
            @foreach($fullRotation as $turnaround)
                <li class="list-group-item py-3 {{ $turnaround->id === $booking->id ? 'table-active' : '' }}">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">

                        <!-- Left: flight-strip style info line -->
                        <div class="d-flex flex-wrap align-items-center text-nowrap">

                            <!-- Step badge -->
                            <span class="badge badge-secondary mr-2" title="{{ __('Flight :n of rotation', ['n' => $loop->iteration]) }}">
                                {{ $loop->iteration }}
                            </span>

                            <!-- Callsign -->
                            <span class="font-weight-bold mr-2">
                                <i class="fas fa-plane mr-1"></i>{{ $turnaround->callsign ?: '—' }}
                            </span>
                            @if($turnaround->id === $booking->id)
                                <span class="text-muted small mr-2">({{ __('your flight') }})</span>
                            @endif

                            @if($turnaround->first_flight && $turnaround->first_flight->airportDep && $turnaround->first_flight->airportArr)
                                <span class="text-muted mx-2">|</span>
                                <span class="mr-2">
                                    {{ $turnaround->first_flight->airportDep->icao }}
                                    <i class="fas fa-long-arrow-alt-right mx-1"></i>
                                    {{ $turnaround->first_flight->airportArr->icao }}
                                </span>
                            @endif

                            @if($turnaround->first_flight && ($turnaround->first_flight->ctot || $turnaround->first_flight->eta))
                                <span class="text-muted mx-2">|</span>
                                <span class="text-muted small">
                                    @if($turnaround->first_flight->ctot)
                                         {{ \Carbon\Carbon::parse($turnaround->first_flight->ctot)->format('H:i') }}z
                                    @endif
                                    @if($turnaround->first_flight->ctot && $turnaround->first_flight->eta)
                                        &nbsp;&middot;&nbsp;
                                    @endif
                                    @if($turnaround->first_flight->eta)
                                        {{ \Carbon\Carbon::parse($turnaround->first_flight->eta)->format('H:i') }}z
                                    @endif
                                </span>
                            @endif
                        </div>

                        <!-- Right: status + action -->
                        <div class="d-flex align-items-center mt-2 mt-md-0">
                            @if($turnaround->status)
                                <span class="{{ $turnaround->statusBadgeClassForUser($booking->user_id) }} mr-2">
                                    {{ $turnaround->statusTextForUser($booking->user_id) }}
                                </span>
                            @endif

                            @if($turnaround->status === BookingStatus::UNASSIGNED)
                                <a href="{{ route('bookings.edit', $turnaround) }}"
                                class="btn btn-sm btn-outline-primary text-nowrap"
                                title="{{ __('Reserve this turnaround slot') }}"
                                target="_blank" rel="noreferrer noopener">
                                    <i class="fas fa-check-circle mr-1" aria-hidden="true"></i>
                                    {{ __('Reserve slot') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="card-body py-2 text-muted small">
            <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
            {{ __('Turnarounds pair consecutive back-to-back flights for the same aircraft.') }}
        </div>
    </div>
@endif
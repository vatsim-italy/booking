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
                    <div class="d-flex flex-wrap align-items-center">
                        <!-- Rotation step + callsign -->
                        <span class="meta-chip mr-3" title="{{ __('Flight :n of rotation', ['n' => $loop->iteration]) }}">
                            <i class="fas fa-plane"></i>{{ $turnaround->callsign ?: '—' }}
                            @if($turnaround->id === $booking->id)
                                <em class="ml-1">({{ __('your flight') }})</em>
                            @endif
                        </span>

                        @if($turnaround->first_flight && $turnaround->first_flight->airportDep && $turnaround->first_flight->airportArr)
                            <span class="meta-chip mr-3" title="{{ __('Route') }}">
                                <i class="fas fa-route"></i>{{ $turnaround->first_flight->airportDep->icao }}
                                – {{ $turnaround->first_flight->airportArr->icao }}
                            </span>
                        @endif

                        @if($turnaround->first_flight && $turnaround->first_flight->ctot)
                            <span class="meta-chip mr-3" title="{{ __('STD') }}">
                                <i class="fas fa-clock"></i>
                                {{ \Carbon\Carbon::parse($turnaround->first_flight->ctot)->format('H:i') }}z
                            </span>
                        @endif

                        @if($turnaround->status)
                            <span class="{{ $turnaround->statusBadgeClassForUser($booking->user_id) }} mr-2">
                                {{ $turnaround->statusTextForUser($booking->user_id) }}
                            </span>
                        @endif

                        <!-- Open slot (absolute URL — fixes 404 from nested pages) -->
                        @unless($turnaround->id === $booking->id)
                            <a href="{{ route('bookings.edit', $turnaround) }}"
                               class="btn btn-sm btn-outline-primary ml-auto text-nowrap"
                               title="{{ __('Open this turnaround slot') }}"
                               target="_blank" rel="noreferrer noopener">
                                <i class="fas fa-external-link-alt mr-1" aria-hidden="true"></i>
                                {{ __('Open slot') }}
                            </a>
                        @endunless
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="card-body py-2 text-muted small">
            <i class="fas fa-info-circle mr-1" aria-hidden="true"></i>
            {{ __('A turnaround connects two flights using the same aircraft. Open a slot to view or book it.') }}
        </div>
    </div>
@endif

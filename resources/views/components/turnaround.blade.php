<!-- Turnaround Information -->
@if($fullRotation && $fullRotation->isNotEmpty() && $fullRotation->count() > 1)
    <div class="mb-4 p-3 bg-light rounded">
        <h6 class="mb-3 border-bottom pb-2">Turnaround Information</h6>

        @foreach($fullRotation as $turnaround)
            <div class="row mb-3">
                @if($turnaround->callsign)
                    <div class="col-md-4">
                        <x-form-group :label="__('Callsign')">
                            <strong>{{ $turnaround->callsign }}</strong>
                            <a href="../{{ $turnaround->uuid }}/edit"
                               rel="noreferrer noopener"
                               target="_blank"
                               class="text-decoration-none ms-2">
                                <i class="fas fa-external-link-alt me-1 text-muted" style="font-size: 0.8rem;"></i>
                            </a>
                        </x-form-group>


                    </div>
                @endif

                @if($turnaround->status)
                    <div class="col-md-4">
                        <x-form-group :label="__('Status')">
                                                <span class="{{ $turnaround->statusBadgeClassForUser($booking->user_id) }}">
                                                    {{ $turnaround->statusTextForUser($booking->user_id) }}
                                                </span>
                        </x-form-group>
                    </div>
                @endif

                @if($turnaround->first_flight && $turnaround->first_flight->airportDep && $turnaround->first_flight->airportArr)
                    <div class="col-md-4">
                        <x-form-group :label="__('Route')">
                            <strong>{{ $turnaround->first_flight->airportDep->icao }} - {{ $turnaround->first_flight->airportArr->icao }}</strong>
                        </x-form-group>
                    </div>
                @endif

                @if($turnaround->first_flight && $turnaround->first_flight->ctot)
                    <div class="col-md-4">
                        <x-form-group :label="__('CTOT')">
                            <strong>{{ $turnaround->first_flight->ctot ? \Carbon\Carbon::parse($turnaround->first_flight->ctot)->format('H:i') : '--:--' }}</strong>
                        </x-form-group>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

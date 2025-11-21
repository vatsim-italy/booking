@extends('layouts.app')

@section('content')
    <x-forms.alert />
    @include('layouts.alert')

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $booking->event->name }} |
                        {{ $booking->status == \App\Enums\BookingStatus::BOOKED ? __('My Booking') : __('My Reservation') }}
                    </h5>
                </div>

                <div class="card-body">
                    <x-form :action="route('bookings.update', $booking)" method="PATCH">
                        @bind($booking)

                        <!-- Flight Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                @if (!$booking->is_editable)
                                    <x-form-group :label="__('Callsign')">
                                        <strong class="text-primary">{{ $booking->formatted_callsign }}</strong>
                                    </x-form-group>
                                @else
                                    <x-form-input name="callsign" :label="__('Callsign')" required maxlength="7" />
                                @endif
                            </div>
                            <div class="col-md-6">
                                <x-form-group :label="__('Aircraft code')">
                                    @if($booking->acType)
                                        <strong>{{ $booking->acType }}</strong>
                                    @elseif($booking->aircraftTypeGroup && $booking->aircraftTypeGroup->types->count())
                                        <select name="acType" class="form-control">
                                            @foreach($booking->aircraftTypeGroup->types as $type)
                                                <option value="{{ $type->type }}" {{ old('acType', $booking->acType) === $type->type ? 'selected' : '' }}>
                                                    {{ $type->type }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif($booking->is_editable)
                                        <input type="text" name="acType" class="form-control" placeholder="Enter aircraft type">
                                    @endif
                                </x-form-group>
                            </div>
                        </div>

                        @bind($flight)

                        <!-- Timing Information -->
                        @if($booking->event->uses_times)
                            <div class="row mb-4">
                                @if($flight->ctot)
                                    <div class="col-md-6">
                                        <x-form-group :label="__('CTOT')">
                                            <strong>{{ $flight->formatted_ctot }}</strong>
                                        </x-form-group>
                                    </div>
                                @endif
                                @if($flight->eta)
                                    <div class="col-md-6">
                                        <x-form-group :label="__('ETA')">
                                            <strong>{{ $flight->formatted_eta }}</strong>
                                        </x-form-group>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Airport Information -->
                        <div class="row mb-4">
                            @if($flight->dep)
                                <div class="col-md-6">
                                    <x-form-group :label="__('ADEP')">
                                        <strong>{{ $flight->airportDep->icao }}</strong>
                                        <small class="text-muted d-block">
                                            {{ $flight->airportDep->name }} ({{ $flight->airportDep->iata }})
                                        </small>
                                    </x-form-group>
                                </div>
                            @endif
                            @if($flight->arr)
                                <div class="col-md-6">
                                    <x-form-group :label="__('ADES')">
                                        <strong>{{ $flight->airportArr->icao }}</strong>
                                        <small class="text-muted d-block">
                                            {{ $flight->airportArr->name }} ({{ $flight->airportArr->iata }})
                                        </small>
                                    </x-form-group>
                                </div>
                            @endif
                        </div>

                        <!-- Flight Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <x-form-group :label="__('PIC')">
                                    <strong>{{ $booking->user->pic }}</strong>
                                </x-form-group>
                            </div>
                            <div class="col-md-6">
                                <x-form-group :label="__('Route')">
                                    <strong>{{ $flight->route ?: '-' }}</strong>
                                </x-form-group>
                            </div>
                        </div>



                        <!-- Turnaround Information -->
                        @if($fullRotation && $fullRotation->isNotEmpty())
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
                                                    <strong>{{ $turnaround->first_flight->ctot }}</strong>
                                                </x-form-group>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Oceanic Information -->
                        @if($booking->event->is_oceanic_event)
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <x-form-group :label="__('Track')">
                                        <strong>{{ $flight->oceanicTrack ?: 'T.B.D.' }}</strong>
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group :label="__('Oceanic Entry FL')">
                                        <strong>{{ $flight->formatted_oceanicfl }}</strong>
                                    </x-form-group>
                                </div>
                                <div class="col-md-4">
                                    <x-form-group :label="__('SELCAL')">
                                        <div class="row g-2">
                                            <div class="col">
                                                <x-form-input name="selcal1" placeholder="AB" minlength="2" maxlength="2" class="text-uppercase" />
                                            </div>
                                            <div class="col">
                                                <x-form-input name="selcal2" placeholder="CD" minlength="2" maxlength="2" class="text-uppercase" />
                                            </div>
                                        </div>
                                        <small class="text-muted">Enter your SELCAL code (e.g., AB-CD)</small>
                                    </x-form-group>
                                </div>
                            </div>
                        @else
                            @if($flight->oceanicFL)
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <x-form-group :label="__('Cruise FL')">
                                            <strong>{{ $flight->formatted_oceanicfl }}</strong>
                                        </x-form-group>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <!-- Airport Links -->
                        <div class="mb-4">
                            @if($flight->airportDep->links->count() || $flight->airportArr->links->count())
                                <h6 class="mb-3">Airport Resources</h6>
                            @endif



                            <div class="row">
                                <!-- Departure Airport Links -->
                                @if($flight->dep && $flight->airportDep->links->count())
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header py-2">
                                                <strong>Departure ({{ $flight->airportDep->icao }})</strong>
                                            </div>
                                            <div class="card-body py-2">
                                                @foreach($flight->airportDep->links as $link)
                                                    <div class="mb-2">
                                                        <a href="{{ $link->url }}"
                                                           rel="noreferrer noopener"
                                                           target="_blank"
                                                           class="text-decoration-none d-flex align-items-center">
                                                            <i class="fas fa-external-link-alt me-2 text-muted" style="font-size: 0.8rem;"></i>
                                                            <span>{{ $link->name ?: $link->type->name }}</span>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- Arrival Airport Links -->
                                @if($flight->arr && $flight->airportArr->links->count())
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-header py-2">
                                                <strong>Arrival ({{ $flight->airportArr->icao }})</strong>
                                            </div>
                                            <div class="card-body py-2">
                                                @foreach($flight->airportArr->links as $link)
                                                    <div class="mb-2">
                                                        <a href="{{ $link->url }}"
                                                           rel="noreferrer noopener"
                                                           target="_blank"
                                                           class="text-decoration-none d-flex align-items-center">
                                                            <i class="fas fa-external-link-alt me-2 text-muted" style="font-size: 0.8rem;"></i>
                                                            <span>{{ $link->name ?: $link->type->name }}</span>
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Event Links -->
                        @if($booking->event->links->count())
                            <div class="mb-4">
                                <h6 class="mb-3">Event Resources</h6>
                                <div class="card">
                                    <div class="card-body">
                                        @foreach($booking->event->links as $link)
                                            <div class="mb-2">
                                                <a href="{{ $link->url }}"
                                                   rel="noreferrer noopener"
                                                   target="_blank"
                                                   class="text-decoration-none d-flex align-items-center">
                                                    <i class="fas fa-external-link-alt me-2 text-muted" style="font-size: 0.8rem;"></i>
                                                    <span>{{ $link->name ?: $link->type->name }}</span>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Notes -->
                        @if($flight->notes)
                            <div class="mb-4">
                                <x-form-group :label="__('Notes')">
                                    <strong>{{ $flight->formatted_notes }}</strong>
                                </x-form-group>
                            </div>
                        @endif

                        <!-- Agreement Checkboxes for Reservations -->
                        @if($booking->status === \App\Enums\BookingStatus::RESERVED)
                            <div class="mb-4 p-3 border rounded">
                                <h6 class="mb-3">Confirmation Requirements</h6>
                                <div class="form-check mb-2">
                                    <input type="hidden" name="checkStudy" value="0">
                                    <x-form-checkbox name="checkStudy" required :label="__('I agree to study the provided briefing material')" value="1" />
                                </div>
                                <div class="form-check">
                                    <input type="hidden" name="checkCharts" value="0">
                                    <x-form-checkbox name="checkCharts" required :label="__('I agree to have the applicable charts at hand during the event')" value="1" />
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 mt-4 pt-3 border-top">
                            <x-form-submit class="btn-primary px-4">
                                <i class="fas fa-check me-2"></i>
                                {{ $booking->status === \App\Enums\BookingStatus::RESERVED ? 'Confirm Booking' : 'Update Booking' }}
                            </x-form-submit>

                            @if($booking->status === \App\Enums\BookingStatus::RESERVED)
                                <button type="button" class="btn btn-danger ml-2 px-4"
                                        onclick="event.preventDefault(); document.getElementById('cancel-form').submit();">
                                    <i class="fas fa-times me-2"></i> Cancel Reservation
                                </button>
                            @endif
                        </div>

                        @endbind
                        @endbind
                    </x-form>

                    <x-form :action="route('bookings.cancel', $booking)" id="cancel-form" method="PATCH" style="display: none;"></x-form>
                </div>
            </div>
        </div>
    </div>
@endsection

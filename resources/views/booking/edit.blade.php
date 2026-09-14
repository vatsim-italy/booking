@extends('layouts.app')

@section('content')
    @include('components.forms.alert')
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
                    <form method="POST" action="{{ route('bookings.update', $booking) }}">
                        @csrf
                        @method('PATCH')

                        <!-- Flight Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                @if (!$booking->is_editable)
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('Callsign') }}</label>
                                        <div><strong class="text-primary">{{ $booking->formatted_callsign }}</strong></div>
                                    </div>
                                @else
                                    <div class="mb-3">
                                        <label for="callsign" class="form-label">{{ __('Callsign') }} <span class="text-danger">*</span></label>
                                        <input type="text" name="callsign" id="callsign" required maxlength="7"
                                               value="{{ old('callsign', $booking->callsign) }}"
                                               class="form-control @error('callsign') is-invalid @enderror">
                                        @include('partials.field-error', ['name' => 'callsign'])
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <label class="form-label">{{ __('Aircraft code') }}</label>
                                    @if($booking->acType)
                                        <div><strong>{{ $booking->acType }}</strong></div>
                                    @elseif($booking->aircraftTypeGroup && $booking->aircraftTypeGroup->types->count())
                                        <select name="acType" class="form-select @error('acType') is-invalid @enderror">
                                            @foreach($booking->aircraftTypeGroup->types as $type)
                                                <option value="{{ $type->type }}" {{ old('acType', $booking->acType) === $type->type ? 'selected' : '' }}>
                                                    {{ $type->type }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @include('partials.field-error', ['name' => 'acType'])
                                    @elseif($booking->is_editable)
                                        <input type="text" name="acType" class="form-control @error('acType') is-invalid @enderror"
                                               value="{{ old('acType') }}" placeholder="Enter aircraft type">
                                        @include('partials.field-error', ['name' => 'acType'])
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Timing Information -->
                        @if($booking->event->uses_times)
                            <div class="row mb-4">
                                @if($flight->ctot)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('STD') }}</label>
                                        <div><strong>{{ $flight->formatted_ctot }}</strong></div>
                                    </div>
                                @endif
                                @if($flight->eta)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('STA') }}</label>
                                        <div><strong>{{ $flight->formatted_eta }}</strong></div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Airport Information -->
                        <div class="row mb-4">
                            @if($flight->dep)
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('ADEP') }}</label>
                                    <div>
                                        <strong>{{ $flight->airportDep->icao }}</strong>
                                        <small class="text-muted d-block">
                                            {{ $flight->airportDep->name }} ({{ $flight->airportDep->iata }})
                                        </small>
                                    </div>
                                </div>
                            @endif
                            @if($flight->arr)
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('ADES') }}</label>
                                    <div>
                                        <strong>{{ $flight->airportArr->icao }}</strong>
                                        <small class="text-muted d-block">
                                            {{ $flight->airportArr->name }} ({{ $flight->airportArr->iata }})
                                        </small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Flight Details -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">{{ __('PIC') }}</label>
                                <div><strong>{{ $booking->user->pic }}</strong></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">{{ __('Route') }}</label>
                                <div><strong>{{ $flight->route ?: '-' }}</strong></div>
                            </div>
                        </div>

                        @component('components.turnaround', [
                            'fullRotation' => $fullRotation,
                            'booking' => $booking
                        ])
                        @endcomponent

                        <!-- Oceanic Information -->
                        @if($booking->event->is_oceanic_event)
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Track') }}</label>
                                    <div><strong>{{ $flight->oceanicTrack ?: 'T.B.D.' }}</strong></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Oceanic Entry FL') }}</label>
                                    <div><strong>{{ $flight->formatted_oceanicfl }}</strong></div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('SELCAL') }}</label>
                                    <div class="row g-2">
                                        <div class="col">
                                            <input type="text" name="selcal1" placeholder="AB" minlength="2" maxlength="2"
                                                   value="{{ old('selcal1') }}"
                                                   class="form-control text-uppercase @error('selcal1') is-invalid @enderror">
                                            @include('partials.field-error', ['name' => 'selcal1'])
                                        </div>
                                        <div class="col">
                                            <input type="text" name="selcal2" placeholder="CD" minlength="2" maxlength="2"
                                                   value="{{ old('selcal2') }}"
                                                   class="form-control text-uppercase @error('selcal2') is-invalid @enderror">
                                            @include('partials.field-error', ['name' => 'selcal2'])
                                        </div>
                                    </div>
                                    <small class="text-muted">Enter your SELCAL code (e.g., AB-CD)</small>
                                </div>
                            </div>
                        @else
                            @if($flight->oceanicFL)
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('Cruise FL') }}</label>
                                        <div><strong>{{ $flight->formatted_oceanicfl }}</strong></div>
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
                                                            <i class="fas fa-external-link-alt mr-2 text-muted" style="font-size: 0.8rem;"></i>
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
                                                            <i class="fas fa-external-link-alt mr-2 text-muted" style="font-size: 0.8rem;"></i>
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
                                                    <i class="fas fa-external-link-alt mr-2 text-muted" style="font-size: 0.8rem;"></i>
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
                                <label class="form-label">{{ __('Notes') }}</label>
                                <div><strong>{{ $flight->formatted_notes }}</strong></div>
                            </div>
                        @endif

                        <!-- Agreement Checkboxes for Reservations -->
                        @if($booking->status === \App\Enums\BookingStatus::RESERVED)
                            <div class="mb-4 p-3 border rounded">
                                <h6 class="mb-3">Confirmation Requirements</h6>
                                <div class="form-check mb-2">
                                    <input type="hidden" name="checkStudy" value="0">
                                    <input class="form-check-input @error('checkStudy') is-invalid @enderror" type="checkbox"
                                           name="checkStudy" id="checkStudy" value="1" required {{ old('checkStudy') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="checkStudy">
                                        {{ __('I agree to study the provided briefing material') }}
                                    </label>
                                    @include('partials.field-error', ['name' => 'checkStudy'])
                                </div>
                                <div class="form-check">
                                    <input type="hidden" name="checkCharts" value="0">
                                    <input class="form-check-input @error('checkCharts') is-invalid @enderror" type="checkbox"
                                           name="checkCharts" id="checkCharts" value="1" required {{ old('checkCharts') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="checkCharts">
                                        {{ __('I agree to have the applicable charts at hand during the event') }}
                                    </label>
                                    @include('partials.field-error', ['name' => 'checkCharts'])
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3 mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-check mr-2"></i>
                                {{ $booking->status === \App\Enums\BookingStatus::RESERVED ? 'Confirm Booking' : 'Update Booking' }}
                            </button>

                            @if($booking->status === \App\Enums\BookingStatus::RESERVED)
                                <button type="button" class="btn btn-danger ml-2 px-4"
                                        onclick="document.getElementById('cancel-form').submit();">
                                    <i class="fas fa-times mr-2"></i> Cancel Reservation
                                </button>
                            @endif
                        </div>
                    </form>

                    <form action="{{ route('bookings.cancel', $booking) }}" id="cancel-form" method="POST" style="display: none;">
                        @csrf
                        @method('PATCH')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
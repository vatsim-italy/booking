@extends('layouts.app')

@section('content')
    @include('components.forms.alert')
    @include('layouts.alert')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $booking->event->name }} |
                    {{ $booking->status == \App\Enums\BookingStatus::BOOKED ? __('My Booking') : __('My Reservation') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('bookings.update', $booking) }}">
                        @csrf
                        @method('PATCH')

                        @foreach ($booking->flights as $flight)
                            <div class="mb-2">
                                <strong>{{ __('Leg #:number', ['number' => $loop->iteration]) }}</strong>
                            </div>

                            @if ($booking->event->uses_times)
                                @if ($flight->ctot)
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('CTOT') }}</label>
                                        <div><strong>{{ $flight->formatted_ctot }}</strong></div>
                                    </div>
                                @endif

                                @if ($flight->eta)
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('ETA') }}</label>
                                        <div><strong>{{ $flight->formatted_eta }}</strong></div>
                                    </div>
                                @endif
                            @endif

                            @if ($flight->dep)
                                <div class="mb-2">
                                    <label class="form-label">{{ __('ADEP') }}</label>
                                    <div><strong>{{ $flight->airportDep->icao }} - {{ $flight->airportDep->name }} -
                                        {{ $flight->airportDep->iata }}</strong></div>
                                </div>
                            @endif

                            @if ($flight->arr)
                                <div class="mb-2">
                                    <label class="form-label">{{ __('ADES') }}</label>
                                    <div><strong>{{ $flight->airportArr->icao }} - {{ $flight->airportArr->name }} -
                                        {{ $flight->airportArr->iata }}</strong></div>
                                </div>
                            @endif

                            <div class="mb-2">
                                <label class="form-label">{{ __('Route') }}</label>
                                <div><strong>{{ $flight->route ?: '-' }}</strong></div>
                            </div>

                            @if ($booking->event->is_oceanic_event)
                                <div class="mb-2">
                                    <label class="form-label">{{ __('Track') }}</label>
                                    <div><strong>{{ $flight->oceanicTrack ?: 'T.B.D.' }}</strong></div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label">{{ __('Oceanic Entry FL') }}</label>
                                    <div><strong>{{ $flight->formatted_oceanicfl }}</strong></div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">{{ __('SELCAL') }}</label>
                                    <div class="d-flex gap-2">
                                        <div>
                                            <input type="text" name="selcal1" placeholder="AB" minlength="2" maxlength="2"
                                                   value="{{ old('selcal1') }}"
                                                   class="form-control @error('selcal1') is-invalid @enderror">
                                            @include('partials.field-error', ['name' => 'selcal1'])
                                        </div>
                                        <div>
                                            <input type="text" name="selcal2" placeholder="CD" minlength="2" maxlength="2"
                                                   value="{{ old('selcal2') }}"
                                                   class="form-control @error('selcal2') is-invalid @enderror">
                                            @include('partials.field-error', ['name' => 'selcal2'])
                                        </div>
                                    </div>
                                </div>
                            @else
                                @if ($flight->oceanicFL)
                                    <div class="mb-2">
                                        <label class="form-label">{{ __('Cruise FL') }}</label>
                                        <div><strong>{{ $flight->formatted_oceanicfl }}</strong></div>
                                    </div>
                                @endif
                            @endif

                            @if ($flight->notes)
                                <div class="mb-2">
                                    <label class="form-label">{{ __('Notes') }}</label>
                                    <div><strong>{{ $flight->formatted_notes }}</strong></div>
                                </div>
                            @endif
                            <hr />
                        @endforeach

                        @if (!$booking->is_editable)
                            <div class="mb-2">
                                <label class="form-label">{{ __('Callsign') }}</label>
                                <div><strong>{{ $booking->formatted_callsign }}</strong></div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label">{{ __('Aircraft code') }}</label>
                                <div><strong>{{ $booking->formatted_actype }}</strong></div>
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="callsign" class="form-label">{{ __('Callsign') }} <span class="text-danger">*</span></label>
                                <input type="text" name="callsign" id="callsign" required maxlength="7"
                                       value="{{ old('callsign', $booking->callsign) }}"
                                       class="form-control @error('callsign') is-invalid @enderror">
                                @include('partials.field-error', ['name' => 'callsign'])
                            </div>
                            <div class="mb-3">
                                <label for="acType" class="form-label">{{ __('Aircraft code') }} <span class="text-danger">*</span></label>
                                <input type="text" name="acType" id="acType" required minlength="3" maxlength="4"
                                       value="{{ old('acType', $booking->acType) }}"
                                       class="form-control @error('acType') is-invalid @enderror">
                                @include('partials.field-error', ['name' => 'acType'])
                            </div>
                        @endif

                        <div class="mb-2">
                            <label class="form-label">{{ __('PIC') }}</label>
                            <div><strong>{{ $booking->user->pic }}</strong></div>
                        </div>

                        @if ($booking->status === \App\Enums\BookingStatus::RESERVED)
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input @error('checkStudy') is-invalid @enderror" type="checkbox"
                                           name="checkStudy" id="checkStudy" value="1" required {{ old('checkStudy') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="checkStudy">
                                        {{ __('I agree to study the provided briefing material') }}
                                    </label>
                                    @include('partials.field-error', ['name' => 'checkStudy'])
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input @error('checkCharts') is-invalid @enderror" type="checkbox"
                                           name="checkCharts" id="checkCharts" value="1" required {{ old('checkCharts') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="checkCharts">
                                        {{ __('I agree to have the applicable charts at hand during the event') }}
                                    </label>
                                    @include('partials.field-error', ['name' => 'checkCharts'])
                                </div>
                            </div>
                        @endif

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i>
                                {{ $booking->status === \App\Enums\BookingStatus::RESERVED ? 'Confirm' : 'Edit' }} Booking
                            </button>

                            @if ($booking->status === \App\Enums\BookingStatus::RESERVED)
                                <button type="button" class="btn btn-danger"
                                        onclick="document.getElementById('cancel-form').submit();">
                                    <i class="fa fa-times"></i> Cancel Reservation
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
@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    @push('scripts')
        <script>
            $('.cancel-booking').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure',
                    text: 'Are you sure you want to cancel your booking?',
                    icon: 'warning',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        Swal.fire('Cancelling booking...');
                        Swal.showLoading();
                        $('#cancel-booking').submit();
                    }
                });
            });
        </script>
    @endpush
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $booking->event->name }} |
                    {{ $booking->status == \App\Enums\BookingStatus::BOOKED ? __('My Booking') : __('My Reservation') }}
                </div>

                <div class="card-body">

                    @foreach ($booking->flights as $flight)
                        <div class="form-group">
                            <strong>
                                {{ __('Leg #:number', ['number' => $loop->iteration]) }}
                            </strong>
                        </div>
                        @if ($booking->event->uses_times)
                            <div class="row">
                                @if ($flight->ctot)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('CTOT') }}</label>
                                            <p class="form-control-plaintext"><strong>{{ $flight->formatted_ctot }}</strong></p>
                                        </div>
                                    </div>
                                @endif

                                @if ($flight->eta)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('ETA') }}</label>
                                            <p class="form-control-plaintext"><strong>{{ $flight->formatted_eta }}</strong></p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="row">
                            @if ($flight->dep)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('ADEP') }}</label>
                                        <p class="form-control-plaintext"><strong>{{ $flight->airportDep->icao }} - {{ $flight->airportDep->name }} -
                                            {{ $flight->airportDep->iata }}</strong></p>
                                    </div>
                                </div>
                            @endif

                            @if ($flight->arr)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('ADES') }}</label>
                                        <p class="form-control-plaintext"><strong>{{ $flight->airportArr->icao }} - {{ $flight->airportArr->name }} -
                                            {{ $flight->airportArr->iata }}</strong></p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label>{{ __('Route') }}</label>
                            <p class="form-control-plaintext"><strong>{{ $flight->route ?: '-' }}</strong></p>
                        </div>

                        @if ($booking->event->is_oceanic_event)
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Track') }}</label>
                                        <p class="form-control-plaintext"><strong>{{ $flight->oceanicTrack ?: 'T.B.D.' }}</strong></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Oceanic Entry FL') }}</label>
                                        <p class="form-control-plaintext"><strong>{{ $flight->formatted_oceanicfl }}</strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('SELCAL') }}</label>
                                <div class="d-flex flex-row">
                                    <input type="text" name="selcal1" class="form-control mr-2" placeholder="AB" minlength="2" maxlength="2" style="max-width: 80px;">
                                    <input type="text" name="selcal2" class="form-control" placeholder="CD" minlength="2" maxlength="2" style="max-width: 80px;">
                                </div>
                            </div>
                        @else
                            @if ($flight->oceanicFL)
                                <div class="form-group">
                                    <label>{{ __('Cruise FL') }}</label>
                                    <p class="form-control-plaintext"><strong>{{ $flight->formatted_oceanicfl }}</strong></p>
                                </div>
                            @endif
                        @endif

                        @if ($flight->notes)
                            <div class="form-group">
                                <label>{{ __('Notes') }}</label>
                                <p class="form-control-plaintext"><strong>{{ $flight->formatted_notes }}</strong></p>
                            </div>
                        @endif
                        <hr />
                    @endforeach

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Callsign') }}</label>
                                <p class="form-control-plaintext"><strong>{{ $booking->formatted_callsign }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Aircraft code') }}</label>
                                <p class="form-control-plaintext"><strong>{{ $booking->formatted_actype }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-row">
                        @if ($booking->is_editable)
                            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary mr-2">
                                {{ __('Edit Booking') }}
                            </a>
                        @endif

                        <button class="btn btn-danger cancel-booking" form="cancel-booking">
                            {{ __('Cancel Booking') }}
                        </button>
                    </div>

                    <form action="{{ route('bookings.cancel', $booking) }}" id="cancel-booking" method="POST" style="display: none;">
                        @csrf
                        @method('PATCH')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


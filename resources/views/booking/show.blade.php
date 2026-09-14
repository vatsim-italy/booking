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
                <!-- Card Header -->
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        {{ $booking->event->name }} |
                        {{ $booking->status == \App\Enums\BookingStatus::BOOKED ? __('My Booking') : __('My Reservation') }}
                    </h5>
                    <span class="{{ $booking->statusBadgeClassForUser(auth()->id()) }} ml-2 text-nowrap">
                        {{ $booking->statusTextForUser(auth()->id()) }}
                    </span>
                </div>

                <div class="card-body">
                    <!-- Flight Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Callsign') }}</label>
                                <p class="form-control-plaintext"><strong class="text-primary">{{ $booking->formatted_callsign }}</strong></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ __('Aircraft code') }}</label>
                                <p class="form-control-plaintext"><strong>{{ $booking->acType ?: 'N/A' }}</strong></p>
                            </div>
                        </div>
                    </div>

                    <!-- Timing Information -->
                    @if($booking->event->uses_times)
                        <div class="row mb-4">
                    <!-- Airport Information -->
                    <div class="row mb-4">
                        @if($flight->dep)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('ADEP') }}</label>
                                    <p class="form-control-plaintext"><strong>{{ $flight->airportDep->icao }} - {{ $flight->airportDep->name }}</strong></p>
                                </div>
                            </div>
                        @endif
                        @if($flight->arr)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ __('ADES') }}</label>
                                    <p class="form-control-plaintext"><strong>{{ $flight->airportArr->icao }} - {{ $flight->airportArr->name }}</strong></p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Route and Oceanic -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{ __('Route') }}</label>
                                <p class="form-control-plaintext"><strong>{{ $flight->route ?: '-' }}</strong></p>
                            </div>
                        </div>
                    </div>

                    @if($booking->event->is_oceanic_event)
                        <div class="row mb-4">
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
                    @else
                        @if ($flight->oceanicFL)
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('Cruise FL') }}</label>
                                        <p class="form-control-plaintext"><strong>{{ $flight->formatted_oceanicfl }}</strong></p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    <!-- Airport Links -->
                    <div class="mb-4">
                        <label class="d-block mb-3 border-bottom pb-2">{{ __('Airport Resources') }}</label>
                        <div class="row">
                            <!-- Departure Airport Links -->
                            @if($flight->dep && $flight->airportDep->links->count())
                                <div class="col-md-6 mb-3 mb-md-0">
                                    <div class="card h-100 shadow-sm border-0">
                                        <div class="card-header py-2 d-flex align-items-center bg-transparent border-bottom-0">
                                            <i class="fas fa-plane-departure text-primary mr-2" aria-hidden="true"></i>
                                            <strong>{{ __('Departure') }} ({{ $flight->airportDep->icao }})</strong>
                                        </div>
                                        <ul class="list-group list-group-flush border-top">
                                            @foreach($flight->airportDep->links as $link)
                                                <li class="list-group-item py-2 border-0 pl-4">
                                                    <a href="{{ $link->url }}"
                                                       rel="noreferrer noopener"
                                                       target="_blank"
                                                       class="text-decoration-none d-flex align-items-center">
                                                        <i class="fas fa-external-link-alt mr-2 text-muted small" aria-hidden="true"></i>
                                                        <span>{{ $link->name ?: $link->type->name }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            <!-- Arrival Airport Links -->
                            @if($flight->arr && $flight->airportArr->links->count())
                                <div class="col-md-6">
                                    <div class="card h-100 shadow-sm border-0">
                                        <div class="card-header py-2 d-flex align-items-center bg-transparent border-bottom-0">
                                            <i class="fas fa-plane-arrival text-primary mr-2" aria-hidden="true"></i>
                                            <strong>{{ __('Arrival') }} ({{ $flight->airportArr->icao }})</strong>
                                        </div>
                                        <ul class="list-group list-group-flush border-top">
                                            @foreach($flight->airportArr->links as $link)
                                                <li class="list-group-item py-2 border-0 pl-4">
                                                    <a href="{{ $link->url }}"
                                                       rel="noreferrer noopener"
                                                       target="_blank"
                                                       class="text-decoration-none d-flex align-items-center">
                                                        <i class="fas fa-external-link-alt mr-2 text-muted small" aria-hidden="true"></i>
                                                        <span>{{ $link->name ?: $link->type->name }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Notes -->
                    @if($flight->notes)
                        <div class="mb-4">
                            <div class="form-group">
                                <label>{{ __('Notes') }}</label>
                                <p class="form-control-plaintext"><strong>{{ $flight->formatted_notes }}</strong></p>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap pt-3 mt-4 border-top">
                        @if($booking->is_editable)
                            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary px-4 mr-2 mb-2 mb-md-0">
                                <i class="fas fa-edit mr-2"></i>{{ __('Edit Booking') }}
                            </a>
                        @endif

                        <button class="btn btn-danger px-4 cancel-booking mb-2 mb-md-0" form="cancel-booking">
                            <i class="fas fa-times mr-2"></i>{{ __('Cancel Booking') }}
                        </button>
                    </div>

                    <!-- Hidden Cancel Form -->
                    <form action="{{ route('bookings.cancel', $booking) }}" id="cancel-booking" method="POST" style="display: none;">
                        @csrf
                        @method('PATCH')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

                    @endif

                            @if($flight->ctot)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('STD') }}</label>
                                        <p class="form-control-plaintext"><strong>{{ $flight->formatted_ctot }}</strong></p>
                                    </div>
                                </div>
                            @endif
                            @if($flight->eta)
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>{{ __('STA') }}</label>
                                        <p class="form-control-plaintext"><strong>{{ $flight->formatted_eta }}</strong></p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif


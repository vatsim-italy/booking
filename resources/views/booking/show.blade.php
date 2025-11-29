@extends('layouts.app')

@section('content')
    <x-forms.alert />

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
                <div class="card-header">
                    <h5 class="mb-0">
                        {{ $booking->event->name }} |
                        {{ $booking->status == \App\Enums\BookingStatus::BOOKED ? __('My Booking') : __('My Reservation') }}
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Flight Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <x-form-group :label="__('Callsign')">
                                <strong class="text-primary">{{ $booking->formatted_callsign }}</strong>
                            </x-form-group>
                        </div>
                        <div class="col-md-6">
                            <x-form-group :label="__('Aircraft code')">
                                <strong>{{ $booking->acType ?: 'N/A' }}</strong>
                            </x-form-group>
                        </div>
                    </div>

                    <!-- Timing Information -->
                    @if($booking->event->uses_times)
                        <div class="row mb-4">
                            @if($flight->ctot)
                                <div class="col-md-6">
                                    <x-form-group :label="__('STD')">
                                        <strong>{{ $flight->formatted_ctot }}</strong>
                                    </x-form-group>
                                </div>
                            @endif
                            @if($flight->eta)
                                <div class="col-md-6">
                                    <x-form-group :label="__('STA')">
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
                                        {{ $flight->airportDep->name }}
                                    </small>
                                </x-form-group>
                            </div>
                        @endif
                        @if($flight->arr)
                            <div class="col-md-6">
                                <x-form-group :label="__('ADES')">
                                    <strong>{{ $flight->airportArr->icao }}</strong>
                                    <small class="text-muted d-block">
                                        {{ $flight->airportArr->name }}
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

                    @component('components.turnaround', [
                        'fullRotation' => $fullRotation,
                        'booking' => $booking
                    ])
                    @endcomponent

                    <!-- Dispatch Options -->
                    <div class="mb-4">
                        <x-form-group :label="__('Flight Planning')">
                            @php
                                $simbriefUrl = 'https://dispatch.simbrief.com/options/custom?';
                                preg_match('/^([A-Z]{2,3})(\d+).*$/', $booking->formatted_callsign, $matches);
                                $airline = $matches[1] ?? '';
                                $fltnum = $matches[2] ?? '';

                                $params = [
                                    'type' => $booking->acType,
                                    'route' => $flight->route,
                                    'airline' => $airline,
                                    'fltnum' => $fltnum,
                                    'orig' => $flight->airportDep->icao,
                                    'dest' => $flight->airportArr->icao,
                                    'callsign' => $booking->formatted_callsign,
                                    'date' => $flight->ctot ? \Carbon\Carbon::parse($flight->ctot)->format('dMy') : null,
                                    'steh'    => $flight->ctot ? \Carbon\Carbon::parse($flight->ctot)->format('H') : null,
                                    'stem'    => $flight->ctot ? \Carbon\Carbon::parse($flight->ctot)->format('i') : null,
                                    'deph'    => $flight->ctot ? \Carbon\Carbon::parse($flight->ctot)->format('H') : null,
                                    'depm'    => $flight->ctot ? \Carbon\Carbon::parse($flight->ctot)->format('i') : null,
                                ];
                            @endphp

                            <a href="{{ $simbriefUrl . http_build_query($params) }}"
                               target="_blank"
                               class="btn btn-outline-primary btn-sm d-inline-flex align-items-center">

                                Open in SimBrief
                            </a>
                            <small class="text-muted d-block mt-1">Pre-filled with your flight details</small>
                        </x-form-group>
                    </div>

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
                                <x-form-group :label="__('SELCAL')" inline>
                                    <strong>{{ $flight->booking->formatted_selcal }}</strong>
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
                        <h6 class="mb-3">Airport Resources</h6>

                        <!-- if none of those conditions are met, show N/A-->
                        @if(
                            (!$flight->dep || !$flight->airportDep->links->count()) &&
                            (!$booking->event->links->count()) &&
                            (!$flight->arr || !$flight->airportArr->links->count())
                        )
                            <p class="text-muted">N/A</p>
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

                            @if($booking->event->links->count())
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body py-2">
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

                    <!-- Notes -->
                    @if($flight->notes)
                        <div class="mb-4">
                            <x-form-group :label="__('Notes')">
                                <strong>{{ $flight->formatted_notes }}</strong>
                            </x-form-group>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="d-inline-flex mt-4 pl-1 pt-3 border-top">
                        @if($booking->is_editable)
                            <a href="{{ route('bookings.edit', $booking) }}" class="btn btn-primary px-4">
                                <i class="fas fa-edit me-2"></i>{{ __('Edit Booking') }}
                            </a>
                        @endif

                        <button class="btn btn-danger px-4 ml-1 cancel-booking" form="cancel-booking">
                            <i class="fas fa-times me-2"></i>{{ __('Cancel Booking') }}
                        </button>
                    </div>

                    <!-- Hidden Cancel Form -->
                    <x-form :action="route('bookings.cancel', $booking)" id="cancel-booking" method="PATCH" style="display: none;"></x-form>
                </div>
            </div>
        </div>
    </div>
@endsection

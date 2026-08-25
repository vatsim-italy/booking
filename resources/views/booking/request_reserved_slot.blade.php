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
                                <x-form-input name="callsign" :label="__('Callsign')" required maxlength="7" />
                            </div>
                            <div class="col-md-6">
                                <x-form-group :label="__('Aircraft code')">
                                    <x-form-input name="acType" class="form-control" placeholder="Enter aircraft type" />
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

                            <!-- ADEP -->
                            <div class="col-md-6">
                                <x-form-group :label="__('ADEP')">
                                    @if ($flight->dep && $flight->airportDep->icao !== 'ZZZZ')
                                        <div class="p-2 border rounded bg-light">
                                            <strong>{{ $flight->airportDep->icao }}</strong>
                                            <small class="text-muted d-block">{{ $flight->airportDep->name }}</small>
                                        </div>
                                    @else
                                        <input 
                                            type="text" 
                                            name="dep" 
                                            id="dep" 
                                            class="form-control" 
                                            placeholder="{{ __('Search departure...') }}" 
                                            autocomplete="off"
                                        />
                                        <div id="dep-suggestions" class="list-group position-absolute" style="z-index: 1000;"></div>
                                    @endif
                                </x-form-group>
                            </div>

                            <!-- ADES -->
                            <div class="col-md-6">
                                <x-form-group :label="__('ADES')">
                                    @if ($flight->arr && $flight->airportArr->icao !== 'ZZZZ')
                                        <div class="p-2 border rounded bg-light">
                                            <strong>{{ $flight->airportArr->icao }}</strong>
                                            <small class="text-muted d-block">{{ $flight->airportArr->name }}</small>
                                        </div>
                                    @else
                                        <input 
                                            type="text" 
                                            name="arr" 
                                            id="arr" 
                                            class="form-control" 
                                            placeholder="{{ __('Search destination...') }}" 
                                            autocomplete="off"
                                        />
                                        <div id="arr-suggestions" class="list-group position-absolute" style="z-index: 1000;"></div>
                                    @endif
                                </x-form-group>
                            </div>

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
                                @if($flight->arr && $flight->airportDep->links->count())
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

                        <div class="d-flex gap-3 mt-4 pt-3 border-top">

                            {{-- USER BUTTONS --}}
                            @if($booking->status === \App\Enums\BookingStatus::UNASSIGNED)
                                <x-form-submit class="btn-primary px-4">
                                    <i class="fas fa-check mr-2"></i> Request Booking
                                </x-form-submit>
                            @endif

                            @if($booking->status === \App\Enums\BookingStatus::RESERVED)
                                <x-form-submit class="btn-primary px-4">
                                    <i class="fas fa-check mr-2"></i> Confirm Booking
                                </x-form-submit>

                                <button type="button"
                                        class="btn btn-danger px-4"
                                        style="margin-left: 0.75rem;"
                                        onclick="event.preventDefault(); document.getElementById('cancel-form').submit();">
                                    <i class="fas fa-times mr-2"></i> Cancel Reservation
                                </button>
                            @endif

                        </div>

                        @endbind
                        @endbind
                    </x-form>

                    @if(auth()->check() && auth()->user()->isAdmin && $booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL)
                        <div class="d-flex gap-3 mt-4 pt-3 border-top">
                            <!-- Approve -->
                            <x-form method="POST" :action="route('admin.bookings.approve', $booking)" class="d-inline">
                                @csrf
                                <button class="btn btn-success px-4">
                                    <i class="fas fa-check mr-2"></i> Approve Booking
                                </button>
                            </x-form>

                            <!-- Decline -->
                            <x-form method="POST" :action="route('admin.bookings.decline', $booking)" class="d-inline">
                                @csrf
                                <button class="btn btn-danger ml-1 px-4">
                                    <i class="fas fa-times mr-2"></i> Decline
                                </button>
                            </x-form>
                        </div>
                    @endif

                    <x-form :action="route('bookings.cancel', $booking)" id="cancel-form" method="PATCH" style="display: none;"></x-form>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
<script>
const airports = @json($airports);

function setupAutocomplete(inputId, suggestionsId) {
    const input = document.getElementById(inputId);
    if (!input) return;

    const suggestionsBox = document.getElementById(suggestionsId);

    // Convert object to array once
    const airportsArray = Object.values(airports);

    input.addEventListener('input', function() {
        const query = this.value.trim().toUpperCase();
        if (query.length < 1) {
            suggestionsBox.innerHTML = '';
            return;
        }

        // Filter the array
        const matches = airportsArray
            .filter(a => a.icao.startsWith(query) || a.name.toUpperCase().includes(query))
            .slice(0, 10); // limit to 10 suggestions

        suggestionsBox.innerHTML = matches.map(a => `
            <button type="button" class="list-group-item list-group-item-action" data-icao="${a.icao}">
                ${a.display}
            </button>
        `).join('');

        suggestionsBox.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('click', function() {
                input.value = this.dataset.icao || this.textContent.split(' | ')[0];
                suggestionsBox.innerHTML = '';
            });
        });
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !suggestionsBox.contains(e.target)) {
            suggestionsBox.innerHTML = '';
        }
    });
}

setupAutocomplete('dep', 'dep-suggestions');
setupAutocomplete('arr', 'arr-suggestions');
</script>
@endpush
@endsection

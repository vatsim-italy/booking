@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
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
                    <form action="{{ route('bookings.update', $booking) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <!-- Flight Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="callsign">{{ __('Callsign') }}</label>
                                    <input type="text" name="callsign" id="callsign" value="{{ old('callsign', $booking->callsign) }}" class="form-control @error('callsign') is-invalid @enderror" required maxlength="7">
                                    @include('partials.field-error', ['name' => 'callsign'])
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="acType">{{ __('Aircraft code') }}</label>
                                    <input type="text" name="acType" id="acType" value="{{ old('acType', $booking->acType) }}" class="form-control @error('acType') is-invalid @enderror" placeholder="Enter aircraft type">
                                    @include('partials.field-error', ['name' => 'acType'])
                                </div>
                            </div>
                        </div>

                        <!-- Timing Information -->
                        @if($booking->event->uses_times)
                            <div class="row mb-4">
                                @if($flight->ctot)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('CTOT') }}</label>
                                            <p class="form-control-plaintext"><strong>{{ $flight->formatted_ctot }}</strong></p>
                                        </div>
                                    </div>
                                @endif
                                @if($flight->eta)
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ __('ETA') }}</label>
                                            <p class="form-control-plaintext"><strong>{{ $flight->formatted_eta }}</strong></p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <!-- Airport Information -->
                        <div class="row mb-4">
                            <!-- ADEP -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dep">{{ __('ADEP') }}</label>
                                    @if ($flight->dep && $flight->airportDep->icao !== 'ZZZZ')
                                        <div class="p-2 border rounded bg-light">
                                            <strong>{{ $flight->airportDep->icao }}</strong>
                                            <small class="text-muted d-block">{{ $flight->airportDep->name }}</small>
                                        </div>
                                    @else
                                        <input type="text" name="dep" id="dep" value="{{ old('dep', $flight->dep) }}" class="form-control @error('dep') is-invalid @enderror" placeholder="Enter ADEP" autocomplete="off">
                                        <div id="dep-suggestions" class="list-group position-absolute w-100 z-index-100 shadow-sm"></div>
                                        @include('partials.field-error', ['name' => 'dep'])
                                    @endif
                                </div>
                            </div>

                            <!-- ADES -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="arr">{{ __('ADES') }}</label>
                                    @if ($flight->arr && $flight->airportArr->icao !== 'ZZZZ')
                                        <div class="p-2 border rounded bg-light">
                                            <strong>{{ $flight->airportArr->icao }}</strong>
                                            <small class="text-muted d-block">{{ $flight->airportArr->name }}</small>
                                        </div>
                                    @else
                                        <input type="text" name="arr" id="arr" value="{{ old('arr', $flight->arr) }}" class="form-control @error('arr') is-invalid @enderror" placeholder="Enter ADES" autocomplete="off">
                                        <div id="arr-suggestions" class="list-group position-absolute w-100 z-index-100 shadow-sm"></div>
                                        @include('partials.field-error', ['name' => 'arr'])
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Oceanic Information -->
                        @if($booking->event->is_oceanic_event)
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="oceanicTrack">{{ __('Track') }}</label>
                                        <input type="text" name="oceanicTrack" id="oceanicTrack" value="{{ old('oceanicTrack', $flight->oceanicTrack) }}" class="form-control @error('oceanicTrack') is-invalid @enderror" maxlength="2">
                                        @include('partials.field-error', ['name' => 'oceanicTrack'])
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="oceanicFL">{{ __('Oceanic Entry Level') }}</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">FL</span>
                                            </div>
                                            <input type="text" name="oceanicFL" id="oceanicFL" value="{{ old('oceanicFL', $flight->oceanicFL) }}" class="form-control @error('oceanicFL') is-invalid @enderror">
                                        </div>
                                        @include('partials.field-error', ['name' => 'oceanicFL'])
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Route and Notes -->
                        <div class="form-group mb-4">
                            <label for="route">{{ __('Route') }}</label>
                            <textarea name="route" id="route" class="form-control @error('route') is-invalid @enderror" rows="3">{{ old('route', $flight->route) }}</textarea>
                            @include('partials.field-error', ['name' => 'route'])
                        </div>

                        <div class="form-group mb-4">
                            <label for="notes">{{ __('Notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $flight->notes) }}</textarea>
                            @include('partials.field-error', ['name' => 'notes'])
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-check mr-2"></i> 
                                {{ $booking->status == \App\Enums\BookingStatus::BOOKED ? __('Update Booking') : __('Update Reservation') }}
                            </button>

                            @if($booking->status == \App\Enums\BookingStatus::RESERVED)
                                <button type="button" class="btn btn-danger px-4" 
                                        onclick="event.preventDefault(); document.getElementById('cancel-form').submit();">
                                    <i class="fas fa-times mr-2"></i> {{ __('Cancel Reservation') }}
                                </button>
                            @endif
                        </div>
                    </form>

                    @if(auth()->check() && auth()->user()->isAdmin && $booking->status === \App\Enums\BookingStatus::PENDING_APPROVAL)
                        <div class="d-flex gap-3 mt-4 pt-3 border-top">
                            <!-- Approve -->
                            <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success px-4">
                                    <i class="fas fa-check mr-2"></i> Approve Booking
                                </button>
                            </form>

                            <!-- Decline -->
                            <form action="{{ route('admin.bookings.decline', $booking) }}" method="POST" class="d-inline ml-2">
                                @csrf
                                <button type="submit" class="btn btn-danger px-4">
                                    <i class="fas fa-times mr-2"></i> Decline
                                </button>
                            </form>
                        </div>
                    @endif

                    <form action="{{ route('bookings.cancel', $booking) }}" id="cancel-form" method="POST" style="display: none;">
                        @csrf
                        @method('PATCH')
                    </form>
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

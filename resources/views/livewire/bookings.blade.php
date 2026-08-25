<div {{ $refreshInSeconds ? "wire:poll.{$refreshInSeconds}s" : '' }}>
    <div class="page-header">
        <i class="fas fa-plane-departure page-header-icon"></i>
        <h3>{{ $event->name }} | {{ $filter ? ucfirst($filter) : 'Slot Table' }}</h3>
    </div>

    <!-- Livewire refresh indicator -->
    <div wire:loading class="text-muted small my-1">
        <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
        Updating slots...
    </div>

    <p>
        @if($event->hasOrderButtons())
            <button wire:model="filter" wire:click="filter(null)"
                class="btn {{ !$filter ? 'btn-success' : 'btn-primary' }}">Show
                All</button>&nbsp;
            <button wire:model="filter" wire:click="filter('departures')"
                class="btn {{ $filter == 'departures' ? 'btn-success' : 'btn-primary' }}">Show
                Departures</button>&nbsp;
            <button wire:model="filter" wire:click="filter('arrivals')"
                class="btn {{ $filter == 'arrivals' ? 'btn-success' : 'btn-primary' }}">Show
                Arrivals</button>&nbsp;
        @endif
        @push('scripts')
        <script>
            function applyFilters() {
                const depFilter = $('#depFilter').val()?.toUpperCase();
                const arrFilter = $('#arrFilter').val()?.toUpperCase();
                const statusFilter = $('#statusFilter').val();
                const reservedToggle = $('#reservedToggle').is(':checked');

                $('table tbody tr').each(function() {
                    const rowDep = $(this).data('dep')?.toUpperCase();
                    const rowArr = $(this).data('arr')?.toUpperCase();
                    const rowStatus = $(this).data('status')?.toString();
                    const rowReserved = $(this).data('reserved-type') === 1;

                    let show = true;
                    if (statusFilter && rowStatus !== statusFilter) show = false;
                    if (depFilter && rowDep !== depFilter) show = false;
                    if (arrFilter && rowArr !== arrFilter) show = false;
                    if (reservedToggle && rowReserved !== reservedToggle) show = false;

                    $(this).toggle(show);
                });
            }

            $(document).on('input change', '#depFilter, #arrFilter, #statusFilter', applyFilters);
            $(document).on('change', '#reservedToggle', applyFilters);
            $(document).on('click', '#resetFilters', function() {
                $('#depFilter').val('');
                $('#arrFilter').val('');
                $('#statusFilter').val('');
                $('#reservedToggle').prop('checked', false);
                applyFilters();
            });

            // Reapply filters after Livewire re-render
            document.addEventListener('livewire:update', applyFilters);
        </script>
        @endpush
        @if(auth()->check() && auth()->user()->isAdmin && $event->endBooking >= now())
            @push('scripts')
                <script>
                    $('.delete-booking').on('click', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Are you sure',
                            text: 'Are you sure you want to remove this booking?',
                            icon: 'warning',
                            showCancelButton: true,
                        }).then((result) => {
                            if (result.value) {
                                Swal.fire('Deleting booking...');
                                Swal.showLoading();
                                $(this).closest('form').submit();
                            }
                        });
                    });

                    $('.decline-booking').on('click', function (e) {
                        e.preventDefault();

                        const url = $(this).attr('href'); // your GET route

                        Swal.fire({
                            title: 'Decline booking?',
                            text: 'Optional: provide a reason for decline.',
                            input: 'text',
                            inputPlaceholder: 'Reason for decline...',
                            inputAttributes: {
                                autocapitalize: 'off'
                            },
                            showCancelButton: true,
                            confirmButtonText: 'Decline',
                            cancelButtonText: 'Cancel',
                            icon: 'warning'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                let finalUrl = url;

                                if (result.value) {
                                    const param = encodeURIComponent(result.value);
                                    finalUrl += (url.includes('?') ? '&' : '?') + 'reason=' + param;
                                }

                                Swal.fire({
                                    title: 'Declining booking...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                        window.location.href = finalUrl;
                                    }
                                });
                            }
                        });
                    });

                </script>
            @endpush
            <a href="{{ route('admin.bookings.create',$event) }}" class="btn btn-primary"><i class="fa fa-plus"></i>
                Add
                Booking</a>&nbsp;
            <a href="{{ route('admin.bookings.create',$event) }}/bulk" class="btn btn-primary"><i
                    class="fa fa-plus"></i>
                Add
                Timeslots</a>&nbsp;
        @endif
    </p>
    @include('layouts.alert')
    <div class="filter-toolbar">
        <div class="d-flex flex-wrap align-items-end">
            <div class="mr-2 mb-2 mb-md-0">
                <label class="filter-label" for="statusFilter">{{ __('Status') }}</label>
                <select name="status" class="custom-select custom-select-sm w-auto" id="statusFilter">
                    <option value="" disabled="" selected="selected">
                        Choose Status
                    </option>
                    <option value="">
                        All Status
                    </option>
                    <option value="0">
                        Available
                    </option>
                    <option value="1">
                        Reserved
                    </option>
                    <option value="2">
                        Booked / My Booking
                    </option>
                    <option value="3">
                        Pending Approval
                    </option>
                </select>
            </div>

            <!-- Departure filter -->
            <div class="mr-2 mb-2 mb-md-0">
                <label class="filter-label" for="depFilter">{{ __('Departure') }}</label>
                <input type="text" id="depFilter" class="form-control form-control-sm w-auto"
                    placeholder="Departure ICAO" aria-label="Departure ICAO filter">
            </div>

            <!-- Arrival filter -->
            <div class="mr-2 mb-2 mb-md-0">
                <label class="filter-label" for="arrFilter">{{ __('Arrival') }}</label>
                <input type="text" id="arrFilter" class="form-control form-control-sm w-auto"
                    placeholder="Arrival ICAO" aria-label="Arrival ICAO filter">
            </div>

            <div class="form-check mr-2 mb-2 mb-md-0">
                <input class="form-check-input" type="checkbox" value="" id="reservedToggle">
                <label class="form-check-label" for="reservedToggle">
                    Custom Only
                </label>
            </div>

            <!-- Reset button -->
            <button type="button" id="resetFilters" class="btn btn-secondary btn-sm"
                aria-label="Reset filters">
                <i class="fas fa-undo mr-1"></i>RESET
            </button>
        </div>
    </div>

@php
    $user = auth()->user();

    $effectiveStart = ($user && $user->is_preaccess && $event->preBooking)
        ? $event->preBooking
        : $event->startBooking;
@endphp

@can('bookingOpen', $event)
    <span class="slots-counter">
        {{ __('Flights available') }}&nbsp;<strong>{{ $total - $booked }}</strong>&nbsp;/&nbsp;{{ $total }}
    </span>
    <div class="table-responsive">
        <table class="table table-hover table-ui">
            @if($event->event_type_id == \App\Enums\EventType::MULTIFLIGHTS->value)
                @include('booking.overview.multiflights')
            @else
                @include('booking.overview.default')
            @endif
        </table>
    </div>
@else
    <h3>
        Bookings will be available at
        <strong>{{ $effectiveStart->format('d-m-Y H:i') }}z</strong>
    </h3>
@endif
</div>

@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    @include('layouts.alert')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $event->name }} | {{ __('Import') }}</div>

                <div class="card-body">
                    <form action="{{ route('admin.bookings.import', $event) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        {{-- File --}}
                        <div class="form-group">
                            <label for="file">{{ __('File') }}</label>
                            <input type="file" name="file" id="file" class="form-control-file @error('file') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'file'])
                        </div>

                        <div class="form-group">
                            <label>{!! __('Headers in <strong>bold</strong> are mandatory') !!}</label>
                            <p class="form-control-plaintext">
                                @if ($event->event_type_id == \App\Enums\EventType::MULTIFLIGHTS->value)
                                    <strong><abbr title="[hh:mm]">CTOT 1</abbr></strong> - <strong><abbr title="[ICAO]">Airport 1</abbr></strong> -
                                    <strong><abbr title="[hh:mm]">CTOT 2</abbr></strong> - <strong><abbr title="[ICAO]">Airport 2</abbr></strong> -
                                    <strong><abbr title="[ICAO]">Airport 3</abbr></strong>
                                @else
                                    Call Sign | <strong><abbr title="[ICAO]">Origin</abbr></strong> |
                                    <strong><abbr title="[ICAO]">Destination</abbr></strong> |
                                    <abbr title="[hh:mm]">CTOT</abbr> | <abbr title="[hh:mm]">ETA</abbr> |
                                    <abbr title="[ICAO]">Aircraft Type</abbr> | Route | Notes | Track | <abbr
                                        title="Max 3 numbers. Examples: 370">FL</abbr>
                                @endif
                            </p>
                        </div>

                        <div class="form-group d-flex align-items-center">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-check"></i> Import
                            </button>

                            <a class="btn btn-secondary"
                                href="{{ $event->event_type_id == \App\Enums\EventType::MULTIFLIGHTS->value ? url('import_multi_flights_template.xlsx') : url('import_template.xlsx') }}">
                                <i class="fas fa-file-excel"></i> Download template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


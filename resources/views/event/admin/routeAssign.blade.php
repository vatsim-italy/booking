@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    @include('layouts.alert')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $event->name }} | {{ __('Import') }}</div>

                <div class="card-body">
                    <form action="{{ route('admin.bookings.routeAssign', $event) }}" method="POST" enctype="multipart/form-data">
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
                                <strong><abbr title="[ICAO]">From</abbr></strong> |
                                <strong><abbr title="[ICAO]">To</abbr></strong> |
                                <strong>Route</strong> |
                                Notes
                            </p>
                        </div>

                        <div class="form-group d-flex align-items-center">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fas fa-check"></i> Auto-Assign Routes
                            </button>
                            <a class="btn btn-secondary"
                                href="{{ url('import_multi_flights_assign_routes_template.xlsx') }}">
                                <i class="fas fa-file-excel"></i> Download template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


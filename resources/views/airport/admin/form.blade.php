@extends('layouts.app')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @include('components.forms.alert', ['errors' => $errors])

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $airport->id ? __('Edit') : __('Add new') }} {{ __('Airport') }}</div>

                <div class="card-body">
                    <form method="POST"
                          action="{{ $airport->id ? route('admin.airports.update', $airport) : route('admin.airports.store') }}">
                        @csrf
                        @if ($airport->id)
                            @method('PATCH')
                        @endif

                        <div class="mb-3">
                            <label for="icao" class="form-label">{{ __('ICAO') }} <span class="text-danger">*</span></label>
                            <input type="text" name="icao" id="icao" maxlength="4" required
                                   value="{{ old('icao', $airport->icao) }}"
                                   class="form-control @error('icao') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'icao'])
                        </div>

                        <div class="mb-3">
                            <label for="iata" class="form-label">{{ __('IATA') }} <span class="text-danger">*</span></label>
                            <input type="text" name="iata" id="iata" maxlength="3" required
                                   value="{{ old('iata', $airport->iata) }}"
                                   class="form-control @error('iata') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'iata'])
                        </div>

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }} <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name', $airport->name) }}"
                                   class="form-control @error('name') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'name'])
                        </div>

                        <div class="mb-3">
                            <label for="latitude" class="form-label">{{ __('Latitude') }}</label>
                            <input type="text" name="latitude" id="latitude"
                                   value="{{ old('latitude', $airport->latitude) }}"
                                   class="form-control @error('latitude') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'latitude'])
                        </div>

                        <div class="mb-3">
                            <label for="longitude" class="form-label">{{ __('Longitude') }}</label>
                            <input type="text" name="longitude" id="longitude"
                                   value="{{ old('longitude', $airport->longitude) }}"
                                   class="form-control @error('longitude') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'longitude'])
                        </div>

                        <button type="submit" class="btn btn-primary">
                            @if ($airport->id)
                                <i class="fa fa-check"></i> {{ __('Edit') }}
                            @else
                                <i class="fa fa-plus"></i> {{ __('Add') }}
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
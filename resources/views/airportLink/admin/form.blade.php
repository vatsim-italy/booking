@extends('layouts.app')

@section('content')
    @include('components.forms.alert')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $airportLink->id ? __('Edit') : __('Add new') }} {{ __('Airport Link') }}
                </div>

                <div class="card-body">
                    <form method="POST"
                          action="{{ $airportLink->id ? route('admin.airportLinks.update', $airportLink) : route('admin.airportLinks.store') }}">
                        @csrf
                        @if ($airportLink->id)
                            @method('PATCH')
                        @endif

                        <div class="mb-3">
                            <label for="airportLinkType_id" class="form-label">{{ __('Type') }} <span class="text-danger">*</span></label>
                            <select name="airportLinkType_id" id="airportLinkType_id" required
                                    class="form-select @error('airportLinkType_id') is-invalid @enderror">
                                <option value="" disabled {{ old('airportLinkType_id', $airportLink->airportLinkType_id) ? '' : 'selected' }}>
                                    {{ __('Choose...') }}
                                </option>
                                @foreach ($airportLinkTypes as $type)
                                    <option value="{{ $type->id }}"
                                        {{ (string) old('airportLinkType_id', $airportLink->airportLinkType_id) === (string) $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'airportLinkType_id'])
                        </div>

                        @if ($airportLink->id)
                            <div class="mb-3">
                                <label class="form-label">{{ __('Airport') }}</label>
                                <div class="form-control-plaintext">
                                    {{ $airportLink->airport->icao }} [{{ $airportLink->airport->name }}
                                    ({{ $airportLink->airport->iata }})]
                                </div>
                            </div>
                        @else
                            <div class="mb-3">
                                <label for="airport_id" class="form-label">{{ __('Airport') }} <span class="text-danger">*</span></label>
                                <select name="airport_id" id="airport_id" required
                                        class="form-select @error('airport_id') is-invalid @enderror">
                                    <option value="" disabled {{ old('airport_id') ? '' : 'selected' }}>
                                        {{ __('Choose...') }}
                                    </option>
                                    @foreach ($airports as $airport)
                                        <option value="{{ $airport->id }}"
                                            {{ (string) old('airport_id') === (string) $airport->id ? 'selected' : '' }}>
                                            {{ $airport->icao }} [{{ $airport->name }} ({{ $airport->iata }})]
                                        </option>
                                    @endforeach
                                </select>
                                @include('partials.field-error', ['name' => 'airport_id'])
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name', $airportLink->name) }}"
                                   class="form-control @error('name') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'name'])
                        </div>

                        <div class="mb-3">
                            <label for="url" class="form-label">{{ __('URL') }} <span class="text-danger">*</span></label>
                            <input type="text" name="url" id="url" required placeholder="https://example.org"
                                   value="{{ old('url', $airportLink->url) }}"
                                   class="form-control @error('url') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'url'])
                        </div>

                        <button type="submit" class="btn btn-primary">
                            @if ($airportLink->id)
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
@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    @include('layouts.alert')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $event->name }} | {{ __('Auto-Assign FL / Route') }}</div>

                <div class="card-body">
                    <form action="{{ route('admin.bookings.autoAssign', $event) }}" method="POST">
                        @csrf
                        {{-- Track 1 --}}
                        <div class="form-group">
                            <label for="oceanicTrack1">{{ __('Track #:number', ['number' => 1]) }}</label>
                            <input type="text" name="oceanicTrack1" id="oceanicTrack1" value="{{ old('oceanicTrack1') }}" class="form-control @error('oceanicTrack1') is-invalid @enderror" required maxlength="2">
                            @include('partials.field-error', ['name' => 'oceanicTrack1'])
                        </div>

                        {{-- Route 1 --}}
                        <div class="form-group">
                            <label for="route1">{{ __('Route #:number', ['number' => 1]) }}</label>
                            <textarea name="route1" id="route1" class="form-control @error('route1') is-invalid @enderror">{{ old('route1') }}</textarea>
                            @include('partials.field-error', ['name' => 'route1'])
                        </div>

                        {{-- Track 2 --}}
                        <div class="form-group">
                            <label for="oceanicTrack2">{{ __('Track #:number', ['number' => 2]) }}</label>
                            <input type="text" name="oceanicTrack2" id="oceanicTrack2" value="{{ old('oceanicTrack2') }}" class="form-control @error('oceanicTrack2') is-invalid @enderror" required maxlength="2">
                            @include('partials.field-error', ['name' => 'oceanicTrack2'])
                        </div>


                        {{-- Route 2 --}}
                        <div class="form-group">
                            <label for="route2">{{ __('Route #:number', ['number' => 2]) }}</label>
                            <textarea name="route2" id="route2" class="form-control @error('route2') is-invalid @enderror">{{ old('route2') }}</textarea>
                            @include('partials.field-error', ['name' => 'route2'])
                        </div>

                        {{-- Min/Max FL --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="minFL">{{ __('Minimum Oceanic Entry FL') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">FL</span>
                                    </div>
                                    <input type="text" name="minFL" id="minFL" value="{{ old('minFL', '320') }}" class="form-control @error('minFL') is-invalid @enderror" required minlength="3" maxlength="3">
                                </div>
                                @include('partials.field-error', ['name' => 'minFL'])
                            </div>

                            <div class="form-group col-md-6">
                                <label for="maxFL">{{ __('Maximum Oceanic Entry FL') }}</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">FL</span>
                                    </div>
                                    <input type="text" name="maxFL" id="maxFL" value="{{ old('maxFL', '380') }}" class="form-control @error('maxFL') is-invalid @enderror" required minlength="3" maxlength="3">
                                </div>
                                @include('partials.field-error', ['name' => 'maxFL'])
                            </div>
                        </div>

                        {{-- Assign all --}}
                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="checkAssignAllFlights" id="checkAssignAllFlights" value="1" class="custom-control-input @error('checkAssignAllFlights') is-invalid @enderror">
                                <label class="custom-control-label" for="checkAssignAllFlights">{{ __('Auto-assign all flights?') }}</label>
                                <small class="form-text text-muted">
                                    {{ __('When enabled, all flights, regardless of being booked will be auto-assigned') }}
                                </small>
                            </div>
                            @include('partials.field-error', ['name' => 'checkAssignAllFlights'])
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> {{ __('Auto-Assign FL / Route') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

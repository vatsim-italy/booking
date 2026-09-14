@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $event->name }} | {{ $bulk ? __('Add Timeslots') : __('Add Slot') }}</div>

                <div class="card-body">
                    <form action="{{ route('admin.bookings.store', $event) }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $event->id }}">
                        <input type="hidden" name="bulk" value="{{ $bulk ? 1 : 0 }}">

                        <div class="form-group">
                            <label>{{ __('Event') }}</label>
                            <p class="form-control-plaintext">
                                {{ $event->name }} [{{ $event->startEvent->format('d-m-Y') }} |
                                {{ $event->startEvent->format('Hi') }}z -
                                {{ $event->endEvent->format('Hi') }}z]
                            </p>
                        </div>

                        {{-- Editable? --}}
                        <div class="form-group">
                            <label>{{ __('Editable?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="is_editable" id="is_editable_no" value="0" class="custom-control-input @error('is_editable') is-invalid @enderror" {{ old('is_editable') == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_editable_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_editable" id="is_editable_yes" value="1" class="custom-control-input @error('is_editable') is-invalid @enderror" {{ old('is_editable') == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_editable_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'is_editable'])
                            <small class="form-text text-muted">
                                {{ __('Choose if you want the booking to be editable (Callsign and Aircraft Code only) by users. This is useful when using \'import only\', but want to add extra slots') }}
                            </small>
                        </div>

                        {{-- Reserved slot? --}}
                        <div class="form-group">
                            <label>{{ __('Reserved slot?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="is_request_slot" id="is_request_slot_no" value="0" class="custom-control-input @error('is_request_slot') is-invalid @enderror" {{ old('is_request_slot') == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_request_slot_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_request_slot" id="is_request_slot_yes" value="1" class="custom-control-input @error('is_request_slot') is-invalid @enderror" {{ old('is_request_slot') == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_request_slot_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'is_request_slot'])
                            <small class="form-text text-muted">
                                {{ __('Choose if you want the booking to be reserved and then manually verified by a staff member (Callsign, Aircraft Code, DEP or ARR airport editable by users).') }}
                            </small>
                        </div>

                        @if (!$bulk)
                            {{-- Callsign --}}
                            <div class="form-group">
                                <label for="callsign">{{ __('Callsign') }}</label>
                                <input type="text" name="callsign" id="callsign" value="{{ old('callsign') }}" class="form-control @error('callsign') is-invalid @enderror" maxlength="7">
                                @include('partials.field-error', ['name' => 'callsign'])
                            </div>

                            {{-- Aircraft code --}}
                            <div class="form-group">
                                <label for="acType">{{ __('Aircraft code') }}</label>
                                <input type="text" name="acType" id="acType" value="{{ old('acType') }}" class="form-control @error('acType') is-invalid @enderror" minlength="3" maxlength="4">
                                @include('partials.field-error', ['name' => 'acType'])
                            </div>
                        @endif

                        {{-- Departure airport --}}
                        <div class="form-group">
                            <label for="dep">{{ __('Departure airport') }}</label>
                            <select name="dep" id="dep" class="custom-select @error('dep') is-invalid @enderror" required>
                                <option value="">{{ __('Choose...') }}</option>
                                @foreach($airports as $icao => $name)
                                    <option value="{{ $icao }}" {{ old('dep', $event->dep) == $icao ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'dep'])
                        </div>

                        {{-- Arrival airport --}}
                        <div class="form-group">
                            <label for="arr">{{ __('Arrival airport') }}</label>
                            <select name="arr" id="arr" class="custom-select @error('arr') is-invalid @enderror" required>
                                <option value="">{{ __('Choose...') }}</option>
                                @foreach($airports as $icao => $name)
                                    <option value="{{ $icao }}" {{ old('arr', $event->dep) == $icao ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'arr'])
                        </div>

                        @if ($bulk)
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label for="start"><i class="fa fa-clock"></i> {{ __('Start') }}</label>
                                    <div class="input-group">
                                        <input type="time" name="start" id="start" value="{{ old('start') }}" class="form-control @error('start') is-invalid @enderror">
                                        <div class="input-group-append">
                                            <span class="input-group-text">z</span>
                                        </div>
                                        @include('partials.field-error', ['name' => 'start'])
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="end"><i class="fa fa-clock"></i> {{ __('End') }}</label>
                                    <div class="input-group">
                                        <input type="time" name="end" id="end" value="{{ old('end') }}" class="form-control @error('end') is-invalid @enderror">
                                        <div class="input-group-append">
                                            <span class="input-group-text">z</span>
                                        </div>
                                        @include('partials.field-error', ['name' => 'end'])
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="separation">{{ __('Separation (in minutes)') }}</label>
                                    <input type="number" name="separation" id="separation" value="{{ old('separation') }}" class="form-control @error('separation') is-invalid @enderror">
                                    @include('partials.field-error', ['name' => 'separation'])
                                </div>
                            </div>
                        @else
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="ctot"><i class="fa fa-clock"></i> {{ __('CTOT') }}</label>
                                    <div class="input-group">
                                        <input type="time" name="ctot" id="ctot" value="{{ old('ctot') }}" class="form-control @error('ctot') is-invalid @enderror">
                                        <div class="input-group-append">
                                        <span class="input-group-text">z</span>
                                    </div>
                                    @include('partials.field-error', ['name' => 'ctot'])
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="eta"><i class="fa fa-clock"></i> {{ __('ETA') }}</label>
                                <div class="input-group">
                                    <input type="time" name="eta" id="eta" value="{{ old('eta') }}" class="form-control @error('eta') is-invalid @enderror">
                                    <div class="input-group-append">
                                        <span class="input-group-text">z</span>
                                    </div>
                                    @include('partials.field-error', ['name' => 'eta'])
                                </div>
                            </div>
                        </div>

                        {{-- Route --}}
                        <div class="form-group">
                            <label for="route">{{ __('Route') }}</label>
                            <textarea name="route" id="route" class="form-control @error('route') is-invalid @enderror">{{ old('route') }}</textarea>
                            @include('partials.field-error', ['name' => 'route'])
                        </div>

                        {{-- Oceanic Entry Level / Cruise FL --}}
                        <div class="form-group">
                            <label for="oceanicFL">{{ $event->is_oceanic_event ? __('Oceanic Entry Level') : __('Cruise FL') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">FL</span>
                                </div>
                                <input type="text" name="oceanicFL" id="oceanicFL" value="{{ old('oceanicFL') }}" class="form-control @error('oceanicFL') is-invalid @enderror">
                                @include('partials.field-error', ['name' => 'oceanicFL'])
                            </div>
                        </div>
                        {{-- Notes --}}
                        <div class="form-group">
                            <label for="notes">{{ __('Notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                            @include('partials.field-error', ['name' => 'notes'])
                        </div>

                        @if (!$bulk)
                            {{-- Turnaround Callsign --}}
                            <div class="form-group">
                                <label for="turnaroundCS">{{ __('Turnaround Callsign') }}</label>
                                <input type="text" name="turnaroundCS" id="turnaroundCS" value="{{ old('turnaroundCS') }}" class="form-control @error('turnaroundCS') is-invalid @enderror" maxlength="7">
                                <small class="form-text text-muted">
                                    {{ __('When flight is part of a turnaround, put here the next/return callsign flight. Last of rotation must be left empty.') }}
                                </small>
                                @include('partials.field-error', ['name' => 'turnaroundCS'])
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary">
                            @if ($bulk)
                                <i class="fa fa-plus"></i> {{ __('Add timeslots') }}
                            @else
                                <i class="fa fa-plus"></i> {{ __('Add slot') }}
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

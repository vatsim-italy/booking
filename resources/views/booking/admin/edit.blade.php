@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    @include('layouts.alert')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $booking->event->name }} | {{ __('Edit Booking') }}</div>

                <div class="card-body">
                    <form action="{{ route('admin.bookings.update', $booking) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        {{-- Editable? --}}
                        <div class="form-group">
                            <label>{{ __('Editable?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="is_editable" id="is_editable_no" value="0" class="custom-control-input @error('is_editable') is-invalid @enderror" {{ old('is_editable', $booking->is_editable) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_editable_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_editable" id="is_editable_yes" value="1" class="custom-control-input @error('is_editable') is-invalid @enderror" {{ old('is_editable', $booking->is_editable) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_editable_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'is_editable'])
                            <small class="form-text text-muted">
                                {{ __('Choose if you want the booking to be editable (Callsign and Aircraft Code only) by users. This is useful when using \'import only\', but want to add extra slots') }}
                            </small>
                        </div>

                        {{-- Request slot? --}}
                        <div class="form-group">
                            <label>{{ __('Request slot?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="is_request_slot" id="is_request_slot_no" value="0" class="custom-control-input @error('is_request_slot') is-invalid @enderror" {{ old('is_request_slot', $booking->is_request_slot) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_request_slot_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_request_slot" id="is_request_slot_yes" value="1" class="custom-control-input @error('is_request_slot') is-invalid @enderror" {{ old('is_request_slot', $booking->is_request_slot) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_request_slot_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'is_request_slot'])
                            <small class="form-text text-muted">
                                {{ __('Choose if you want the booking to be requested and then manually verified by a staff member (Callsign, Aircraft Code, DEP or ARR airport editable by users).') }}
                            </small>
                        </div>

                        {{-- CTOT & ETA --}}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="ctot"><i class="fa fa-clock"></i> {{ __('CTOT') }}</label>
                                <div class="input-group">
                                    <input type="time" name="ctot" id="ctot" value="{{ old('ctot', $flight->ctot?->format('H:i')) }}" class="form-control @error('ctot') is-invalid @enderror">
                                    <div class="input-group-append">
                                        <span class="input-group-text">z</span>
                                    </div>
                                    @include('partials.field-error', ['name' => 'ctot'])
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="eta"><i class="fa fa-clock"></i> {{ __('ETA') }}</label>
                                <div class="input-group">
                                    <input type="time" name="eta" id="eta" value="{{ old('eta', $flight->eta?->format('H:i')) }}" class="form-control @error('eta') is-invalid @enderror">
                                    <div class="input-group-append">
                                        <span class="input-group-text">z</span>
                                    </div>
                                    @include('partials.field-error', ['name' => 'eta'])
                                </div>
                            </div>
                        </div>

                        {{-- Departure airport --}}
                        <div class="form-group">
                            <label for="dep">{{ __('Departure airport') }}</label>
                            <select name="dep" id="dep" class="custom-select @error('dep') is-invalid @enderror" required>
                                <option value="">{{ __('Choose...') }}</option>
                                @foreach($airports as $icao => $name)
                                    <option value="{{ $icao }}" {{ old('dep', $flight->dep) == $icao ? 'selected' : '' }}>{{ $name }}</option>
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
                                    <option value="{{ $icao }}" {{ old('arr', $flight->arr) == $icao ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'arr'])
                        </div>

                        {{-- PIC --}}
                        <div class="form-group">
                            <label>{{ __('PIC') }}</label>
                            <p class="form-control-plaintext">{{ $booking->user ? $booking->user->pic : '-' }}</p>
                        </div>

                        {{-- Callsign --}}
                        <div class="form-group">
                            <label for="callsign">{{ __('Callsign') }}</label>
                            <input type="text" name="callsign" id="callsign" value="{{ old('callsign', $booking->callsign) }}" class="form-control @error('callsign') is-invalid @enderror" maxlength="7">
                            @include('partials.field-error', ['name' => 'callsign'])
                        </div>

                        {{-- Aircraft code --}}
                        <div class="form-group">
                            <label for="acType">{{ __('Aircraft code') }}</label>
                            <input type="text" name="acType" id="acType" value="{{ old('acType', $booking->acType) }}" class="form-control @error('acType') is-invalid @enderror" minlength="3" maxlength="4">
                            @include('partials.field-error', ['name' => 'acType'])
                        </div>

                        {{-- Route --}}
                        <div class="form-group">
                            <label for="route">{{ __('Route') }}</label>
                            <textarea name="route" id="route" class="form-control @error('route') is-invalid @enderror">{{ old('route', $flight->route) }}</textarea>
                            @include('partials.field-error', ['name' => 'route'])
                        </div>

                        @if ($booking->event->is_oceanic_event)
                            {{-- Track --}}
                            <div class="form-group">
                                <label for="oceanicTrack">{{ __('Track') }}</label>
                                <input type="text" name="oceanicTrack" id="oceanicTrack" value="{{ old('oceanicTrack', $flight->oceanicTrack) }}" class="form-control @error('oceanicTrack') is-invalid @enderror" maxlength="2">
                                @include('partials.field-error', ['name' => 'oceanicTrack'])
                            </div>
                        @endif

                        {{-- Oceanic Entry Level / Cruise FL --}}
                        <div class="form-group">
                            <label for="oceanicFL">{{ $booking->event->is_oceanic_event ? __('Oceanic Entry Level') : __('Cruise FL') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">FL</span>
                                </div>
                                <input type="text" name="oceanicFL" id="oceanicFL" value="{{ old('oceanicFL', $flight->oceanicFL) }}" class="form-control @error('oceanicFL') is-invalid @enderror">
                                @include('partials.field-error', ['name' => 'oceanicFL'])
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="form-group">
                            <label for="notes">{{ __('Notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $flight->notes) }}</textarea>
                            @include('partials.field-error', ['name' => 'notes'])
                        </div>

                        {{-- Turnaround Callsign --}}
                        <div class="form-group">
                            <label for="turnaroundCS">{{ __('Turnaround Callsign') }}</label>
                            <input type="text" name="turnaroundCS" id="turnaroundCS" value="{{ old('turnaroundCS', $booking->turnaroundCS) }}" class="form-control @error('turnaroundCS') is-invalid @enderror" maxlength="7">
                            <small class="form-text text-muted">
                                {{ __('When flight is part of a turnaround, put here the next/return callsign flight. Last of rotation must be left empty.') }}
                            </small>
                            @include('partials.field-error', ['name' => 'turnaroundCS'])
                        </div>

                        @if ($booking->user_id)
                            {{-- Message --}}
                            <div class="form-group">
                                <label for="message">{{ __('Message') }}</label>
                                <textarea name="message" id="message" class="form-control @error('message') is-invalid @enderror">{{ old('message') }}</textarea>
                                @include('partials.field-error', ['name' => 'message'])
                            </div>

                            {{-- Notify user? --}}
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="notify_user" id="notify_user" value="1" class="custom-control-input @error('notify_user') is-invalid @enderror" {{ old('notify_user', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="notify_user">{{ __('Notify user?') }}</label>
                                </div>
                                @include('partials.field-error', ['name' => 'notify_user'])
                            </div>
                        @endif

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i> {{ __('Update') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

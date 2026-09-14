@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $event->id ? 'Edit' : 'Add new' }} Event</div>

                <div class="card-body">
                    <form action="{{ $event->id ? route('admin.events.update', $event) : route('admin.events.store') }}" method="POST">
                        @csrf
                        @if($event->id)
                            @method('PATCH')
                        @endif

                        {{-- Show online? --}}
                        <div class="form-group">
                            <label>{{ __('Show online?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="is_online" id="is_online_no" value="0" class="custom-control-input @error('is_online') is-invalid @enderror" {{ old('is_online', $event->is_online) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_online_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_online" id="is_online_yes" value="1" class="custom-control-input @error('is_online') is-invalid @enderror" {{ old('is_online', $event->is_online) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_online_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'is_online'])
                            <small class="form-text text-muted">
                                {{ __("Choose here if you want the event to be reachable by it's generated url") }}
                            </small>
                        </div>

                        {{-- Show on homepage? --}}
                        <div class="form-group">
                            <label>{{ __('Show on homepage?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="show_on_homepage" id="show_on_homepage_no" value="0" class="custom-control-input @error('show_on_homepage') is-invalid @enderror" {{ old('show_on_homepage', $event->show_on_homepage) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="show_on_homepage_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="show_on_homepage" id="show_on_homepage_yes" value="1" class="custom-control-input @error('show_on_homepage') is-invalid @enderror" {{ old('show_on_homepage', $event->show_on_homepage) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="show_on_homepage_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'show_on_homepage'])
                            <small class="form-text text-muted">
                                {{ __("Choose here if you want to show the event on the homepage. If turned off, the event can only be reached by the url. NOTE: If 'Show Online' is off, the event won't be shown at all") }}
                            </small>
                        </div>

                        {{-- Name --}}
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $event->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                            @include('partials.field-error', ['name' => 'name'])
                        </div>

                        {{-- Event type --}}
                        <div class="form-group">
                            <label for="event_type_id">{{ __('Event type') }}</label>
                            <select name="event_type_id" id="event_type_id" class="custom-select @error('event_type_id') is-invalid @enderror" required>
                                <option value="">{{ __('Choose...') }}</option>
                                @foreach($eventTypes as $id => $name)
                                    <option value="{{ $id }}" {{ old('event_type_id', $event->event_type_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'event_type_id'])
                        </div>

                        {{-- Only import? --}}
                        <div class="form-group">
                            <label>{{ __('Only import?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="import_only" id="import_only_no" value="0" class="custom-control-input @error('import_only') is-invalid @enderror" {{ old('import_only', $event->import_only) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="import_only_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="import_only" id="import_only_yes" value="1" class="custom-control-input @error('import_only') is-invalid @enderror" {{ old('import_only', $event->import_only) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="import_only_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'import_only'])
                            <small class="form-text text-muted">
                                {{ __('If enabled, only admins can fill in details via import script') }}
                            </small>
                        </div>

                        {{-- Show times? --}}
                        <div class="form-group">
                            <label>{{ __('Show times?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="uses_times" id="uses_times_no" value="0" class="custom-control-input @error('uses_times') is-invalid @enderror" {{ old('uses_times', $event->uses_times) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="uses_times_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="uses_times" id="uses_times_yes" value="1" class="custom-control-input @error('uses_times') is-invalid @enderror" {{ old('uses_times', $event->uses_times) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="uses_times_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'uses_times'])
                            <small class="form-text text-muted">
                                {{ __('If enabled, CTOT and ETA (if set in booking) will be shown') }}
                            </small>
                        </div>

                        {{-- Multiple bookings allowed? --}}
                        <div class="form-group">
                            <label>{{ __('Multiple bookings allowed?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="multiple_bookings_allowed" id="multiple_bookings_allowed_no" value="0" class="custom-control-input @error('multiple_bookings_allowed') is-invalid @enderror" {{ old('multiple_bookings_allowed', $event->multiple_bookings_allowed) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="multiple_bookings_allowed_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="multiple_bookings_allowed" id="multiple_bookings_allowed_yes" value="1" class="custom-control-input @error('multiple_bookings_allowed') is-invalid @enderror" {{ old('multiple_bookings_allowed', $event->multiple_bookings_allowed) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="multiple_bookings_allowed_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'multiple_bookings_allowed'])
                            <small class="form-text text-muted">
                                {{ __('If enabled, a user is allowed to book multiple flights for this event') }}
                            </small>
                        </div>

                        {{-- Oceanic event? --}}
                        <div class="form-group">
                            <label>{{ __('Oceanic event?') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="is_oceanic_event" id="is_oceanic_event_no" value="0" class="custom-control-input @error('is_oceanic_event') is-invalid @enderror" {{ old('is_oceanic_event', $event->is_oceanic_event) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_oceanic_event_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_oceanic_event" id="is_oceanic_event_yes" value="1" class="custom-control-input @error('is_oceanic_event') is-invalid @enderror" {{ old('is_oceanic_event', $event->is_oceanic_event) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_oceanic_event_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'is_oceanic_event'])
                            <small class="form-text text-muted">
                                {{ __('If enabled, users can fill in a SELCAL code') }}
                            </small>
                        </div>

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
                                    <option value="{{ $icao }}" {{ old('arr', $event->arr) == $icao ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'arr'])
                        </div>

                        {{-- Start event --}}
                        <div class="form-group">
                            <label>{{ __('Start event (UTC)') }}</label>
                            <x-flat-pickr name="startEvent" value="{{ old('startEvent', $event->startEvent) }}" />
                            @include('partials.field-error', ['name' => 'startEvent'])
                        </div>

                        {{-- End event --}}
                        <div class="form-group">
                            <label>{{ __('End event (UTC)') }}</label>
                            <x-flat-pickr name="endEvent" value="{{ old('endEvent', $event->endEvent) }}" />
                            @include('partials.field-error', ['name' => 'endEvent'])
                        </div>

                        {{-- Start booking --}}
                        <div class="form-group">
                            <label>{{ __('Start booking (UTC)') }}</label>
                            <x-flat-pickr name="startBooking" value="{{ old('startBooking', $event->startBooking) }}" />
                            @include('partials.field-error', ['name' => 'startBooking'])
                        </div>

                        {{-- End booking --}}
                        <div class="form-group">
                            <label>{{ __('End booking (UTC)') }}</label>
                            <x-flat-pickr name="endBooking" value="{{ old('endBooking', $event->endBooking) }}" />
                            @include('partials.field-error', ['name' => 'endBooking'])
                        </div>

                        {{-- Image URL --}}
                        <div class="form-group">
                            <label for="image_url">{{ __('Image URL') }}</label>
                            <input type="text" name="image_url" id="image_url" value="{{ old('image_url', $event->image_url) }}" class="form-control @error('image_url') is-invalid @enderror" placeholder="https://example.org">
                            @include('partials.field-error', ['name' => 'image_url'])
                        </div>

                        {{-- Event mail --}}
                        <div class="form-group">
                            <label for="mail">{{ __('Event mail') }}</label>
                            <input type="text" name="mail" id="mail" value="{{ old('mail', $event->mail) }}" class="form-control @error('mail') is-invalid @enderror" placeholder="urberealops@vatita.net">
                            @include('partials.field-error', ['name' => 'mail'])
                        </div>

                        {{-- Description --}}
                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea name="description" id="description" class="form-control tinymce @error('description') is-invalid @enderror">{{ old('description', $event->description) }}</textarea>
                            @include('partials.field-error', ['name' => 'description'])
                        </div>

                        <button type="submit" class="btn btn-primary">
                            @if ($event->id)
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

@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    @include('layouts.alert')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">My settings</div>
                <div class="card-body">
                    <form action="{{ route('user.saveSettings') }}" method="POST">
                        @csrf
                        @method('PATCH')

                        {{-- Default airport view --}}
                        <div class="form-group">
                            <label>{{ __('Default airport view') }}</label>
                            <div class="custom-control custom-radio">
                                <input type="radio" name="airport_view" id="airport_view_0" value="0" class="custom-control-input @error('airport_view') is-invalid @enderror" {{ old('airport_view', $user->airport_view) == 0 ? 'checked' : '' }} required>
                                <label class="custom-control-label" for="airport_view_0">{{ __('Name') . ': Amsterdam Airport Schiphol - EHAM | [AMS]' }}</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" name="airport_view" id="airport_view_1" value="1" class="custom-control-input @error('airport_view') is-invalid @enderror" {{ old('airport_view', $user->airport_view) == 1 ? 'checked' : '' }} required>
                                <label class="custom-control-label" for="airport_view_1">{{ __('ICAO') . ': EHAM - Amsterdam Airport Schiphol | [AMS]' }}</label>
                            </div>
                            <div class="custom-control custom-radio">
                                <input type="radio" name="airport_view" id="airport_view_2" value="2" class="custom-control-input @error('airport_view') is-invalid @enderror" {{ old('airport_view', $user->airport_view) == 2 ? 'checked' : '' }} required>
                                <label class="custom-control-label" for="airport_view_2">{{ __('IATA') . ': AMS - Amsterdam Airport Schiphol | [EHAM]' }}</label>
                            </div>
                            @include('partials.field-error', ['name' => 'airport_view'])
                        </div>

                        {{-- Use monospace font --}}
                        <div class="form-group">
                            <label>{{ __('Use monospace font') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="use_monospace_font" id="use_monospace_font_no" value="0" class="custom-control-input @error('use_monospace_font') is-invalid @enderror" {{ old('use_monospace_font', $user->use_monospace_font) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="use_monospace_font_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="use_monospace_font" id="use_monospace_font_yes" value="1" class="custom-control-input @error('use_monospace_font') is-invalid @enderror" {{ old('use_monospace_font', $user->use_monospace_font) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="use_monospace_font_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'use_monospace_font'])
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save settings
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


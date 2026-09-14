@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $eventLink->id ? 'Edit' : 'Add new' }} Event Link</div>

                <div class="card-body">
                    <form action="{{ $eventLink->id ? route('admin.eventLinks.update', $eventLink) : route('admin.eventLinks.store') }}" method="POST">
                        @csrf
                        @if($eventLink->id)
                            @method('PATCH')
                        @endif

                        {{-- Type --}}
                        <div class="form-group">
                            <label for="event_link_type_id">{{ __('Type') }}</label>
                            <select name="event_link_type_id" id="event_link_type_id" class="custom-select @error('event_link_type_id') is-invalid @enderror" required>
                                <option value="">{{ __('Choose...') }}</option>
                                @foreach($eventLinkTypes as $id => $name)
                                    <option value="{{ $id }}" {{ old('event_link_type_id', $eventLink->event_link_type_id) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @include('partials.field-error', ['name' => 'event_link_type_id'])
                        </div>

                        @if ($eventLink->id)
                            <div class="form-group">
                                <label>{{ __('Event') }}</label>
                                <p class="form-control-plaintext">
                                    {{ $eventLink->event->name }} [{{ $eventLink->event->startEvent->format('d-m-Y') }}]
                                </p>
                            </div>
                        @else
                            <div class="form-group">
                                <label for="event_id">{{ __('Event') }}</label>
                                <select name="event_id" id="event_id" class="custom-select @error('event_id') is-invalid @enderror" required>
                                    <option value="">{{ __('Choose...') }}</option>
                                    @foreach($events as $id => $name)
                                        <option value="{{ $id }}" {{ old('event_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @include('partials.field-error', ['name' => 'event_id'])
                            </div>
                        @endif

                        {{-- Name --}}
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $eventLink->name) }}" class="form-control @error('name') is-invalid @enderror">
                            @include('partials.field-error', ['name' => 'name'])
                        </div>

                        {{-- URL --}}
                        <div class="form-group">
                            <label for="url">{{ __('URL') }}</label>
                            <input type="text" name="url" id="url" value="{{ old('url', $eventLink->url) }}" class="form-control @error('url') is-invalid @enderror" placeholder="https://example.org" required>
                            @include('partials.field-error', ['name' => 'url'])
                        </div>

                        <button type="submit" class="btn btn-primary">
                            @if ($eventLink->id)
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


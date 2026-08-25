@extends('layouts.app')

@section('content')
    @include('layouts.alert')

    <div class="page-header">
        <i class="fas fa-calendar-alt page-header-icon"></i>
        <h3>{{ __('Upcoming Events') }}</h3>
        <p class="page-header-subtitle">{{ __('Book your slot for one of the upcoming events') }}</p>
    </div>

    @forelse($events as $event)
        <div class="row event mb-4">
            <div class="col-xl-6 col-lg-6 col-md-12 col-sm-12">
                <div class="card card-hover event-card">
                    @if ($event->image_url)
                        <a href="{{ route('bookings.event.index', $event) }}">
                            <img src="{{ $event->image_url }}" class="event-card-img card-img-top"
                                alt="{{ $event->name }}">
                        </a>
                    @endif
                    <div class="card-body event-card-body">
                        <h4 class="event-title text-primary"><a
                                href="{{ route('bookings.event.index', $event) }}">{{ $event->name }}</a></h4>
                        <div class="my-2">
                            <span class="meta-chip">
                                <i class="fas fa-calendar"></i>{{ $event->startEvent->toFormattedDateString() }}
                            </span>
                            <span class="meta-chip">
                                <i class="fas fa-clock"></i>{{ $event->startEvent->format('H:i\z') }} -
                                {{ $event->endEvent->format('H:i\z') }}
                            </span>
                        </div>
                        {!! $event->description !!}
                    </div>
                    <div class="card-footer bg-white border-0 pb-3">
                        <a href="{{ route('bookings.event.index', $event) }}" class="btn btn-success">
                            <i class="fas fa-chair mr-1"></i> {{ __('See Available Slots') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p class="mb-0">Currently no events scheduled.</p>
        </div>
    @endforelse
@endsection

@extends('layouts.app')

@section('content')
    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @include('layouts.alert')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $event->name }} | Import</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.bookings.import',$event) }}"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{--File--}}
                        <div class="form-group row">

                            <div class="col-md-4 col-form-label text-md-right"></div>
                            <div class="col-md-6">
                                <input type="file" class="form-control-file" id="file" name="file">

                                @if ($errors->has('file'))
                                    <span class="invalid-feedback">
                                        <strong>{{ $errors->first('file') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">

                            <label for="ctot" class="col-md-2 col-form-label text-md-right"> Format</label>
                            <div class="col-md-10">
                                <div class="form-control-plaintext">
                                    @if($event->event_type_id == \App\Enums\EventType::MULTIFLIGHTS)
                                        CTOT 1 - Airport 1 - CTOT 2 - Airport 2 - Airport 3
                                    @else
                                        <strong>Arrivals</strong> - Call Sign | Origin | Destination | ETA | Aircraft
                                        Type
                                        <br>
                                        <strong>Departures</strong> - Call Sign | Origin | Destination | EOBT | Aircraft
                                        Type
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{--Import--}}
                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check"></i> Import
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

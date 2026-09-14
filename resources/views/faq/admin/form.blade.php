@extends('layouts.app')

@section('content')
    @include('components.forms.alert', ['errors' => $errors])
    @include('layouts.alert')
    @push('scripts')
        <script>
            $('.unlink-event').on('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure',
                    text: 'Are you sure you want to unlink this event?',
                    icon: 'warning',
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        Swal.fire('Unlinking event...');
                        Swal.showLoading();
                        $(this).closest('form').submit();
                    }
                });
            });
        </script>
    @endpush
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ $faq->id ? 'Edit' : 'Add new' }} FAQ</div>

                <div class="card-body">
                    <form action="{{ $faq->id ? route('admin.faq.update', $faq) : route('admin.faq.store') }}" method="POST">
                        @csrf
                        @if($faq->id)
                            @method('PATCH')
                        @endif

                        {{-- Is online --}}
                        <div class="form-group">
                            <label>{{ __('Is online') }}</label>
                            <div class="d-flex flex-row flex-wrap">
                                <div class="custom-control custom-radio mr-3">
                                    <input type="radio" name="is_online" id="is_online_no" value="0" class="custom-control-input @error('is_online') is-invalid @enderror" {{ old('is_online', $faq->is_online) == 0 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_online_no">{{ __('No') }}</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" name="is_online" id="is_online_yes" value="1" class="custom-control-input @error('is_online') is-invalid @enderror" {{ old('is_online', $faq->is_online) == 1 ? 'checked' : '' }} required>
                                    <label class="custom-control-label" for="is_online_yes">{{ __('Yes') }}</label>
                                </div>
                            </div>
                            @include('partials.field-error', ['name' => 'is_online'])
                        </div>

                        {{-- Question --}}
                        <div class="form-group">
                            <label for="question">{{ __('Question') }}</label>
                            <input type="text" name="question" id="question" value="{{ old('question', $faq->question) }}" class="form-control @error('question') is-invalid @enderror" required>
                            @include('partials.field-error', ['name' => 'question'])
                        </div>

                        {{-- Answer --}}
                        <div class="form-group">
                            <label for="answer">{{ __('Answer') }}</label>
                            <textarea name="answer" id="answer" class="form-control tinymce @error('answer') is-invalid @enderror">{{ old('answer', $faq->answer) }}</textarea>
                            @include('partials.field-error', ['name' => 'answer'])
                        </div>

                        <button type="submit" class="btn btn-primary">
                            @if ($faq->id)
                                <i class="fa fa-check"></i> {{ __('Edit') }}
                            @else
                                <i class="fa fa-plus"></i> {{ __('Add') }}
                            @endif
                        </button>
                    </form>
                </div>

                @if ($faq->id)
                    <div class="card-header">{{ __('Related events') }}</div>
                    @if ($events)
                        <div class="card-body">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="row">{{ __('Name') }}</th>
                                        <th scope="row">{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                @foreach ($events as $event)
                                    <tr>
                                        <td>{{ $event->name }} [{{ $event->startEvent->format('d-m-Y') }}]</td>
                                        <td>
                                            <form
                                                action="{{ route('admin.faq.toggleEvent', ['faq' => $faq, 'event' => $event]) }}"
                                                method="post">
                                                @csrf
                                                @method('PATCH')

                                                @if ($faq->events()->where('event_id', $event->id)->first())
                                                    <button
                                                        class="btn btn-danger unlink-event">{{ __('Unlink event') }}</button>
                                                @else
                                                    <button class="btn btn-success">{{ __('Link event') }}</button>
                                                @endif
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

